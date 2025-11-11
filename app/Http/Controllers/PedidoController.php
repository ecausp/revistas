<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\PedidoHistorico;
use App\Models\PedidoServico;
use App\Models\Servico;
use App\Models\Solicitante;
use App\Models\Revista;
use App\Models\Licitacao;
use App\Models\Empresa;
use App\Models\Contato;
use App\Models\User;
use App\Models\Arquivo;

use App\States\Concluido;
use Illuminate\Support\Facades\Storage;


use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

use Illuminate\Support\Facades\Mail;
use App\Mail\PedidoDevolvidoParaRevistaMail;
use App\Mail\PedidoEnviadoParaEmpresaMail;
use App\Mail\PedidoEnviadoParaFinanceiroMail;
use App\Mail\PedidoEnviadoParaExecucaoMail;
use App\Mail\PedidoEmConferenciaMail;
use App\Mail\PedidoDevolvidoParaExecucaoMail;
use App\Mail\PedidoPagoMail;

#states
use App\States\EmElaboracao;
use App\States\EmAnaliseEmpresa;
use App\States\EmAnaliseFinanceiro;
use App\States\EmExecucao;
use App\States\EmConferencia;
use App\States\EmPagamento;

class PedidoController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        \UspTheme::activeUrl('pedidos');

        // Obtém a coleção de pedidos que o usuário tem acesso
        $pedidos = $this->getPedidosDoUsuario();

        // Verifica se a coleção está vazia E se o usuário não tem um papel de acesso configurado
        if ($pedidos->isEmpty() && !Solicitante::where('user_id', auth()->id())->exists() && !Contato::where('user_id', auth()->id())->exists() && !auth()->user()->hasRole('admin') && !auth()->user()->hasRole('financeiro'))
        {
            session()->flash('danger', 'Usuário não cadastrado como solicitante. Favor contatar financeiro.');
            return redirect()->route('home');
        }

        return view('pedidos.index', compact('pedidos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // 1. Autorização e Guard (Permissão)
        $this->authorize("acesso-revista");
        $solicitante = $this->handleSolicitanteGuard();

        // Se o Guard retornou um RedirectResponse (não um Solicitante), retornamos o redirecionamento
        if ($solicitante instanceof RedirectResponse) {
            return $solicitante;
        }
        
        // 2. Setup e Configuração
        \UspTheme::activeUrl('pedidos/create');
        $anoAtual = date('Y');
        $licitacao = Licitacao::where('ano', $anoAtual)->first();
        
        // 3. Busca de Dados
        // Pega a revista que ele faz parte
        $revista = Revista::where('id', $solicitante->revista_id)->first();
        
        // Chama o método auxiliar para buscar e mapear os serviços
        $servicosComLimite = $this->getServicosComLimites($revista, $licitacao);

        return view('pedidos.create', compact('solicitante', 'licitacao', 'servicosComLimite'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize("acesso-revista");

        // 1. Regras de validação
        $rules = [
            'solicitante_id' => 'required|exists:solicitantes,id',
            'revista_id' => 'required|exists:revistas,id',
            'licitacao_id' => 'required|exists:licitacoes,id',
            'observacoes' => 'nullable|string',
            'servicos' => 'required|array',
            'servicos.*' => 'exists:servicos,id',
            'quantidades' => 'required|array',
            'quantidades.*' => 'integer|min:1',
            'arquivos' => 'nullable|array',
            'arquivos.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png,svg,tiff|max:3072',
            'descricoes' => 'nullable|array',
            'descricoes.*' => 'required_with:arquivos|string|max:255', 
        ];

        $validated = $request->validate($rules);

        // 2. CRIAÇÃO DO PEDIDO
        $pedido = Pedido::create([
            'licitacao_id' => $validated['licitacao_id'],
            'solicitante_id' => $validated['solicitante_id'],
            'revista_id' => $validated['revista_id'],
            'observacoes' => $validated['observacoes'] ?? null,
        ]);
        $pedido->status = new EmElaboracao($pedido);
        
        // 3. SINCRONIZAÇÃO DE SERVIÇOS
        $this->syncServicosDoPedido($pedido, $validated);

        // 4. UPLOAD DE ARQUIVOS
        $resultadoArquivos = $this->handleArquivosDoPedido($request, $pedido);

        // Verifica se a função de arquivo retornou um RedirectResponse
        if ($resultadoArquivos instanceof RedirectResponse) {
            return $resultadoArquivos;
        }
        $arquivos_salvos = $resultadoArquivos;
        
        // 5. HISTÓRICO E REDIRECIONAMENTO FINAL
        $pedido->adicionarHistorico($pedido->status);

        if(Solicitante::where('user_id', auth()->id())->exists()|| auth()->user()->hasRole('admin'))
        {
            $msg_arquivos = $arquivos_salvos > 0 ? " E $arquivos_salvos arquivo(s) anexado(s)." : '';
            session()->flash('success', 'Pedido cadastrado com sucesso!' . $msg_arquivos);
            return redirect()->route('pedidos.index');
        }
        else
        {
            session()->flash('danger', 'Usuário não cadastrado como solicitante. Favor contatar financeiro.');
            return redirect()->route('home');
        }
    }
    
    /**
     * Anexa MÚLTIPLOS arquivos a um pedido existente (usado na edição).
     * O formulário da edição deve mirar esta rota.
     */
    public function storeArquivo(Request $request, Pedido $pedido)
    {
        // Validação (Apenas arquivos e descrições, mantendo a regra de segurança)
        $rules = [
            'arquivos' => 'required|array',
            'arquivos.*' => 'file|mimes:pdf,jpg,jpeg,png,svg,tiff|max:3072', // 3MB
            'descricoes' => 'required|array',
            'descricoes.*' => 'required|string|max:255',
        ];

        $request->validate($rules);
        
        // 2. Processamento dos Arquivos
        try {
            $files = $request->file('arquivos');
            $descriptions = $request->input('descricoes', []); 
            $arquivos_salvos = 0;

            // CRÍTICO: Checagem de alinhamento N:N
            if (count($files) !== count($descriptions)) {
                 throw new \Exception('Erro de submissão: O número de arquivos e descrições não é compatível.');
            }

            foreach ($files as $index => $file) {
                if ($file && $file->isValid()) {
                    $description = $descriptions[$index]; 
                    
                    $fileName = time() . '_' . $file->getClientOriginalName();
                    // Salva o arquivo no disco 'public'
                    $path = $file->storeAs('arquivos', $fileName, 'public'); 

                    // Cria o registro Arquivo associado ao Pedido
                    Arquivo::create([
                        'pedido_id' => $pedido->id,
                        'nome' => $fileName,
                        'descricao' => $description, 
                        'caminho' => $path,
                    ]);
                    $arquivos_salvos++;
                }
            }
            
            // Resposta de Sucesso (Volta para a tela de edição)
            return back()->with('success', "$arquivos_salvos arquivo(s) anexado(s) com sucesso!");

        } catch (\Exception $e) {
            // Resposta de Falha
            return back()->with('danger', 'Erro ao processar arquivos: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Pedido $pedido)
    {
        $licitacao = $pedido->licitacao;
        $solicitante = $pedido->solicitante;
        $servicos = Servico::all();
        $historico = PedidoHistorico::where('id_pedido', $pedido->id)->orderBy('created_at', 'desc')->get();

        // se for admin ou financeiro pode ver sem restrição
        if(auth()->user()->hasRole('admin') || auth()->user()->hasRole(roles: 'financeiro'))
            return view('pedidos.show', compact('historico','pedido', 'licitacao', 'solicitante', 'servicos'));
        // Se for um solicitante de uma revista, pode visualizar os pedidos da sua revista
        else if(Solicitante::where('user_id', auth()->id())->exists())
        {
            $revista_do_pedido = $pedido->revista->id;
            $revista_do_usuario = Solicitante::where('user_id', auth()->id())->first()->revista->id;
            
            if($revista_do_usuario == $revista_do_pedido)
            {
                return view('pedidos.show', compact('historico','pedido', 'licitacao', 'solicitante', 'servicos'));
            }        
            else
            {
            session()->flash('danger', 'Esse pedido não pertence à sua revista.');
            return redirect()->route('home');

            }
        }
        // se for um contato de uma empresa, só pode visualizar os pedidos que tem serviços da sua empresa
        else if(Contato::where('user_id', auth()->id())->exists())
        {
            $servico_do_pedido = $pedido->servicos->first();
            $empresa_do_pedido = $servico_do_pedido->empresas->pluck('id');
            $empresa_do_contato = Contato::where('user_id', auth()->id())->first()->empresa_id;
            if($empresa_do_pedido->contains($empresa_do_contato))
            {
                return view('pedidos.show', compact('historico','pedido', 'licitacao', 'solicitante', 'servicos'));
            }
            else
            {
                session()->flash('danger', 'Usuário não possui acesso a esse recurso. Favor contatar financeiro.');
                return redirect()->route('home');
            }
        }

        else
        {
            session()->flash('danger', 'Usuário não possui acesso a esse recurso. Favor contatar financeiro.');
            return redirect()->route('home');

        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pedido $pedido)
    {
        $user = auth()->user();
        
        // 1. Autorização: Verifica status do pedido (Guard)
        if ($pedido->status == "em_analise_empresa")
        {
            session()->flash('danger', 'Pedido em análise pela empresa, não pode ser editado.');
            return redirect()->route('home');
        }

        // 2. Autorização: Verifica se o usuário é o solicitante correto (Guard)
        $solicitante = Solicitante::where('user_id', $user->id)->first();
        
        // Se não for um solicitante, ou se for, mas não da revista do pedido
        if (!$solicitante || $pedido->revista->id != $solicitante->revista->id)
        {
            session()->flash('danger', 'Esse usuário não pode editar esse pedido.');
            return redirect()->route('pedidos.index');
        }

        // --- Se a autorização passou, preparamos os dados ---

        $licitacao = $pedido->licitacao;
        $revista = $pedido->revista;
        
        // Carregar a relação 'arquivos' (necessário para o partial de arquivos existentes)
        $pedido->load('arquivos'); 

        // 3. Lógica para obter Serviços com Limite (Reutilizando getServicosComLimites)
        
        // Recria o formato $servicosComLimite para o forms genérico
        $servicosComLimite = $this->getServicosComLimites($revista, $licitacao);
        
        // Carrega as empresas para que o forms possa ler data-empresa (necessário fazer a eager load aqui)
        // Isso é necessário porque getServicosComLimites não carrega empresas, mas sim limites.
        $servicosComLimite->each(function ($item) {
             $item->servico->load('empresas');
        });

        // 5. Retorna a view, passando o pedido, o solicitante (para o forms) e os serviços no formato correto.
        return view('pedidos.edit', compact('pedido', 'solicitante', 'servicosComLimite'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Pedido $pedido)
    {
        // Valide se quiser:
        $request->validate([
            'servicos' => 'array',
            'quantidades' => 'array',
            // Arquivos e descrições não devem estar aqui, pois são submetidos via storeArquivo
        ]);
        
        // Autorização (reforçar)
        $user = auth()->user();
        $solicitante = Solicitante::where('user_id', $user->id)->first();

        if (!$solicitante || $pedido->revista->id != $solicitante->revista->id) {
            session()->flash('danger', 'Usuário não autorizado a atualizar este pedido.');
            return redirect()->route('pedidos.index');
        }

        // Atualiza dados simples
        $pedido->observacoes = $request->observacoes;
        $pedido->revista_id = $request->revista_id;
        $pedido->solicitante_id = $request->solicitante_id;
        $pedido->licitacao_id = $request->licitacao_id;
        $pedido->save();

        // Sincroniza os serviços com a tabela pivot e quantidades
        $syncData = [];

        foreach ($request->servicos ?? [] as $id) {
            // Adicionando o valor unitário ao sync (melhor prática)
            $servico = Servico::find($id);
            $syncData[$id] = [
                'quantidade' => $request->quantidades[$id] ?? 1,
                'valor_unitario' => $servico ? $servico->valor_unitario : 0, 
            ];
        }

        $pedido->servicos()->sync($syncData); 
        
        if(Solicitante::where('user_id', auth()->id())->exists()|| auth()->user()->hasRole('admin'))
            return redirect()->route('pedidos.index')->with('success', 'Pedido atualizado com sucesso!');
        else
        {
            session()->flash('danger', 'Usuário não cadastrado como solicitante. Favor contatar financeiro.');
            return redirect()->route('home');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pedido $pedido)
    {   
        $this->authorize("acesso-revista");

        $pedido->delete();
        
        if(Solicitante::where('user_id', auth()->id())->exists()|| auth()->user()->hasRole('admin'))        
        {
            session()->flash('danger', 'Pedido da Revista: ' . $pedido->revista->name . ' para a Licitação: ' . $pedido->licitacao->descricao . ' removido!');
            return redirect()->route('pedidos.index');
        }
        else
        {
            session()->flash('danger', 'Usuário não cadastrado como solicitante. Favor contatar financeiro.');
            return redirect()->route('home');
        }
    }

    public function updateServicos(Request $request, Pedido $pedido)
    {
        // Validação básica
        $request->validate([
            'servicos' => 'required|array',
            'quantidades' => 'required|array',
        ]);

        $servicos = $request->input('servicos');
        $quantidades = $request->input('quantidades');

        // Atualiza os serviços com as quantidades no relacionamento many-to-many (pivot)
        $syncData = [];
        foreach ($servicos as $servicoId) {
            // Adicionando o valor unitário ao sync (melhor prática)
            $servico = Servico::find($servicoId);
            $syncData[$servicoId] = [
                'quantidade' => $quantidades[$servicoId] ?? 1,
                'valor_unitario' => $servico ? $servico->valor_unitario : 0,
            ];
        }

        $pedido->servicos()->sync($syncData);

        // Recarrega relação para view atualizada
        $pedido->load('servicos');

        // Renderiza a partial da tabela com os serviços atualizados
        $html = view('pedidos.partials.servicos', compact('pedido'))->render();

        // Retorna JSON para o AJAX
        return response()->json([
            'html' => $html,
            'quantidades' => $pedido->servicos->pluck('pivot.quantidade', 'id'),
        ]);
    }

    public function removerServicoDoPedido($pedidoId, $servicoId)
    {
        $pedido = Pedido::findOrFail($pedidoId);
        $pedido->servicos()->detach($servicoId);

        return response()->json(['mensagem' => 'Serviço desvinculado com sucesso!']);
    }

     public function paraElaboracao(Request $request, Pedido $pedido)
    {

        // Troca o status
        $pedido->status->transitionTo(EmElaboracao::class);
        $pedido->save();

        foreach($pedido->revista->solicitantes as $solicitante){
            Mail::to($solicitante->user->email)->send(new PedidoDevolvidoParaRevistaMail($pedido));
        }      
        Mail::to($pedido->revista->email_revista)->send(new PedidoDevolvidoParaRevistaMail($pedido));
        
        $pedido->adicionarHistorico($pedido->status, $request->observacao);

        session()->flash('error', 'Pedido devolvido para elaboração.');
        
        // se o usuário é financeiro ou admin, pode ver todos os pedidos
        if(auth()->user()->hasRole('admin') || auth()->user()->hasRole(roles: 'financeiro'))
        {
            $pedidos = Pedido::all(); 
            return view('pedidos.index', compact('pedidos'));
        }
        // se o usuário é solicitante, pode ver os pedidos da revista dele
        else if(Solicitante::where('user_id', auth()->id())->exists())
        {   
            $solicitante = Solicitante::where('user_id', auth()->id())->first();
            $pedidos = Pedido::where('revista_id', $solicitante->revista_id)->get();  
            return view('pedidos.index', compact('pedidos'));
        }
        // se o usuário é contato, pode ver os pedidos que tem serviço da empresa que ele está associado
        else if(Contato::where('user_id', auth()->id())->first())
        {
            $contato = Contato::where('user_id', auth()->id())->first();
            $empresa = Empresa::where('id', $contato->empresa_id)->first();
            $servicos = $empresa->servicos->pluck('id');
            $pedidos = Pedido::whereHas('servicos', function ($query) use ($servicos) {
                $query->whereIn('servicos.id', $servicos);})->get();
            return view('pedidos.index', compact('pedidos'));
        }
    }

    public function paraAnaliseEmpresa(Pedido $pedido)
    {

        // só pode ser enviado se o usuário for:
            // a) solicitante da revista 
            // b) se a revista do pedido for a revista do usuário
        if(Solicitante::where('user_id', auth()->id())->exists() && $pedido->revista->id == Solicitante::where('user_id', auth()->id())->first()->revista->id)
        {
            // 1. Atualiza o status do pedido
            $pedido->status->transitionTo(EmAnaliseEmpresa::class);
            $pedido->save();


            $pedido->adicionarHistorico($pedido->status);
            
            $servicos = $pedido->servicos()->pluck('servicos.id');        
            $empresas = Empresa::whereHas('servicos', function ($query) use ($servicos) {
                    $query->whereIn('servicos.id', $servicos);})->get();
            $empresas->load('contatos'); // faz uma única query pra todos
            $contatos = $empresas
                ->pluck('contatos')   // pega a coleção de contatos de cada empresa
                ->flatten()           // junta tudo
                ->values();           // reindexa
        
            foreach($contatos as $contato){
                Mail::to($contato->user->email)->send(new PedidoEnviadoParaEmpresaMail($pedido));
            }        

            session()->flash('success', 'Pedido enviado por e-mail para os contatos da empresa responsável.');
            
            // redireciona o usuario:
            $solicitante = Solicitante::where('user_id', auth()->id())->first();
            $pedidos = Pedido::where('revista_id', $solicitante->revista_id)->get();  
            return view('pedidos.index', compact('pedidos'));
        }

        else
        {
            session()->flash('danger', 'Usuário não é solicitante da revista e não pode enviar pedidos para a empresa.');
            return redirect()->route('home');
        }
    }

    public function paraAnaliseFinanceiro(Pedido $pedido)
    {

        // só pode ser enviado se o usuário for contato da empresa que recebeu o pedido
        $contatos = $pedido->servicos()->first()->empresas()->first()->contatos;
        if(Contato::where('user_id', auth()->id())->exists() && $contatos->contains('user_id', auth()->id()))
        {
            // 1. Atualiza o status do pedido
            $pedido->status->transitionTo(EmAnaliseFinanceiro::class);
            $pedido->save();

            $pedido->adicionarHistorico($pedido->status);
        

            $financeiros = User::role('financeiro')->get();
            foreach($financeiros as $financeiro){
                Mail::to($financeiro->email)->send(new PedidoEnviadoParaFinanceiroMail($pedido));
            }        

            session()->flash('success', 'Pedido enviado por e-mail para os responsáveis do setor financeiro.');
            
            // redireciona o usuario:
            $contato = Contato::where('user_id', auth()->id())->first();
            $empresa = Empresa::where('id', $contato->empresa_id)->first();
            $servicos = $empresa->servicos->pluck('id');
            $pedidos = Pedido::whereHas('servicos', function ($query) use ($servicos) {
            $query->whereIn('servicos.id', $servicos);})->get();
            return view('pedidos.index', compact('pedidos'));
        }
        else
        {
            session()->flash('danger', 'Usuário não é representante da empresa e não pode enviar pedidos para o setor financeiro.');
            return redirect()->route('home');
        }
    }


    public function paraExecucao(Request $request, Pedido $pedido)
    {

        // 1) Validação
        $data = $request->validate([
            'empenhos' => 'required|array',
            'empenhos.*' => 'required|string|max:255', // obrigatório
            'num_notificacao_fornecimento' => 'required|string|max:255', // obrigatório
        ]);

        // 2) Atualiza cada registro pivot pedido_servico pelo pivot id
        foreach ($data['empenhos'] as $pivotId => $numEmpenho) {

            $pedidoServico = PedidoServico::where('id', $pivotId)
                                            ->where('pedido_id', $pedido->id)
                                            ->first();

                        
            if (!$pedidoServico) {
                session()->flash('danger', 'Erro: serviço inválido ou não pertence a este pedido.');
                return back();
            }
            $pedidoServico->data_vencimento = $pedido->updated_at->addDays($pedidoServico->servico->prazo)->format('Y-m-d');
            $pedidoServico->num_empenho = $numEmpenho;
            $pedidoServico->save();
        }


        // 3) Atualiza num_notificacao_fornecimento no pedido
        $pedido->num_notificacao_fornecimento = $data['num_notificacao_fornecimento'];

        // 4) Confirma que todos os empenhos foram preenchidos
        $faltando = $pedido->servicos()->wherePivotNull('num_empenho')->count();
        if ($faltando > 0) {
            session()->flash('danger', 'Todos os serviços devem ter um número de empenho antes de enviar para execução.');
            return back();
        }

        // 5) Transição de status para execução
        $pedido->status->transitionTo(EmExecucao::class);

        $pedido->save();
    
        // 6) Adiciona histórico
        $pedido->adicionarHistorico($pedido->status, "Notificação de fornecimento e empenhos emitidos");

        session()->flash('success', 'Pedido enviado para execução.');

        // 7) Notifica os contatos da empresa
        $servicos = $pedido->servicos()->pluck('servicos.id');        
        $empresas = Empresa::whereHas('servicos', function ($query) use ($servicos) {
            $query->whereIn('servicos.id', $servicos);})->get();
        $empresas->load('contatos'); // faz uma única query pra todos
        $contatos = $empresas
            ->pluck('contatos')   // pega a coleção de contatos de cada empresa
            ->flatten()           // junta tudo
            ->values();           // reindexa
            
        foreach($contatos as $contato){
            Mail::to($contato->user->email)->send(new PedidoEnviadoParaExecucaoMail($pedido));
        }     

        // 7) Redireciona conforme papel do usuário
        if(auth()->user()->hasRole('admin') || auth()->user()->hasRole(roles: 'financeiro')) {
            $pedidos = Pedido::all();
            return view('pedidos.index', compact('pedidos'));
        } else if(Solicitante::where('user_id', auth()->id())->exists()) {
            $solicitante = Solicitante::where('user_id', auth()->id())->first();
            $pedidos = Pedido::where('revista_id', $solicitante->revista_id)->get();
            return view('pedidos.index', compact('pedidos'));
        } else if(Contato::where('user_id', auth()->id())->first()) {
            $contato = Contato::where('user_id', auth()->id())->first();
            $empresa = Empresa::where('id', $contato->empresa_id)->first();
            $servicos = $empresa->servicos->pluck('id');
            $pedidos = Pedido::whereHas('servicos', function ($query) use ($servicos) {
                $query->whereIn('servicos.id', $servicos);
            })->get();
            return view('pedidos.index', compact('pedidos'));
        }

    }

    public function paraConferencia(Pedido $pedido)
    {

        // só pode ser enviado se o usuário for contato da empresa que recebeu o pedido
        $contatos = $pedido->servicos()->first()->empresas()->first()->contatos;
        if(Contato::where('user_id', auth()->id())->exists() && $contatos->contains('user_id', auth()->id()))
        {
            // 1. Atualiza o status do pedido
            $pedido->status->transitionTo(EmConferencia::class);
            $pedido->save();

            $pedido->adicionarHistorico($pedido->status);
        
            foreach($pedido->revista->solicitantes as $solicitante){
            Mail::to($solicitante->user->email)->send(new PedidoEmConferenciaMail($pedido));
            }      
            Mail::to($pedido->revista->email_revista)->send(new PedidoEmConferenciaMail($pedido));

            session()->flash('success', 'Pedido enviado por e-mail para os responsáveis da revista.');
            
            // redireciona o usuario:
            $contato = Contato::where('user_id', auth()->id())->first();
            $empresa = Empresa::where('id', $contato->empresa_id)->first();
            $servicos = $empresa->servicos->pluck('id');
            $pedidos = Pedido::whereHas('servicos', function ($query) use ($servicos) {
            $query->whereIn('servicos.id', $servicos);})->get();
            return view('pedidos.index', compact('pedidos'));
        }
        else
        {
            session()->flash('danger', 'Usuário não é representante da empresa e não pode enviar pedidos para o setor financeiro.');
            return redirect()->route('home');
        }
    }

    #se o pedido for rejeitado na conferencia, ele volta para o status 'em_execucao'
    public function pedidoDevolvidoExecucao(Pedido $pedido, Request $request)
    {
        $pedido->status->transitionTo(EmExecucao::class);
        $pedido->save();
        $pedido->adicionarHistorico($pedido->status, $request->observacao);
        
        // Notifica os contatos da empresa
        $servicos = $pedido->servicos()->pluck('servicos.id');        
        $empresas = Empresa::whereHas('servicos', function ($query) use ($servicos) {
            $query->whereIn('servicos.id', $servicos);})->get();
        $empresas->load('contatos'); // faz uma única query pra todos
        $contatos = $empresas
            ->pluck('contatos')   // pega a coleção de contatos de cada empresa
            ->flatten()           // junta tudo
            ->values();           // reindexa
            
        foreach($contatos as $contato){
            Mail::to($contato->user->email)->send(new PedidoDevolvidoParaExecucaoMail($pedido));
        }     

        // Redireciona conforme papel do usuário
        if(auth()->user()->hasRole('admin') || auth()->user()->hasRole(roles: 'financeiro')) {
            $pedidos = Pedido::all();
            return view('pedidos.index', compact('pedidos'));
        }
        else if(Solicitante::where('user_id', auth()->id())->exists()) {
            $solicitante = Solicitante::where('user_id', auth()->id())->first();
            $pedidos = Pedido::where('revista_id', $solicitante->revista_id)->get();
            return view('pedidos.index', compact('pedidos'));
        }
        else if(Contato::where('user_id', auth()->id())->first()) {
            $contato = Contato::where('user_id', auth()->id())->first();
            $empresa = Empresa::where('id', $contato->empresa_id)->first();
            $servicos = $empresa->servicos->pluck('id');
            $pedidos = Pedido::whereHas('servicos', function ($query) use ($servicos) {
                $query->whereIn('servicos.id', $servicos);
            })->get();
            return view('pedidos.index', compact('pedidos'));
        }

        session()->flash('success', 'Pedido devolvido para a empresa.');
        return redirect()->route('pedidos.index');
    }

    public function paraPagamento(Pedido $pedido)
    {
        // só pode ser enviado se o usuário for:
            // a) solicitante da revista 
            // b) se a revista do pedido for a revista do usuário
        if(Solicitante::where('user_id', auth()->id())->exists() && $pedido->revista->id == Solicitante::where('user_id', auth()->id())->first()->revista->id)
        {
            // 1. Atualiza o status do pedido
            $pedido->status->transitionTo(EmPagamento::class);
            $pedido->save();


            $pedido->adicionarHistorico($pedido->status);
            
            $financeiros = User::role('financeiro')->get();
            foreach($financeiros as $financeiro){
                Mail::to($financeiro->email)->send(new PedidoPagoMail($pedido));
            }

            session()->flash('success', 'Pedido enviado por e-mail para os contatos da empresa responsável.');
            
            // redireciona o usuario:
            $solicitante = Solicitante::where('user_id', auth()->id())->first();
            $pedidos = Pedido::where('revista_id', $solicitante->revista_id)->get();  
            return view('pedidos.index', compact('pedidos'));
        }

        else
        {
            session()->flash('danger', 'Usuário não é solicitante da revista e não pode enviar pedidos para o setor financeiro.');
            return redirect()->route('home');
        }
    }

    public function pedidoConcluido(Pedido $pedido)
    {
            $pedido->status->transitionTo(Concluido::class);
            $pedido->save();


            $pedido->adicionarHistorico($pedido->status, 'Pedido Concluido');
            
        // Redireciona conforme papel do usuário
        if(auth()->user()->hasRole('admin') || auth()->user()->hasRole(roles: 'financeiro')) {
            $pedidos = Pedido::all();
            return view('pedidos.index', compact('pedidos'));
        } else if(Solicitante::where('user_id', auth()->id())->exists()) {
            $solicitante = Solicitante::where('user_id', auth()->id())->first();
            $pedidos = Pedido::where('revista_id', $solicitante->revista_id)->get();
            return view('pedidos.index', compact('pedidos'));
        } else if(Contato::where('user_id', auth()->id())->first()) {
            $contato = Contato::where('user_id', auth()->id())->first();
            $empresa = Empresa::where('id', $contato->empresa_id)->first();
            $servicos = $empresa->servicos->pluck('id');
            $pedidos = Pedido::whereHas('servicos', function ($query) use ($servicos) {
                $query->whereIn('servicos.id', $servicos);
            })->get();
            return view('pedidos.index', compact('pedidos'));
        }

    }
    /**
     * Retorna a coleção de Pedidos que o usuário logado tem permissão para visualizar.
     */
private function getPedidosDoUsuario()
    {
        $user = auth()->user();

        // 1. Admin ou Financeiro (vê todos)
        if ($user->hasRole('admin') || $user->hasRole('financeiro')) {

            return Pedido::orderBy('id', 'desc')->get();
        }

        // 2. Solicitante (vê pedidos da sua revista)
        if (Solicitante::where('user_id', $user->id)->exists()) {
            $solicitante = Solicitante::where('user_id', $user->id)->first();

            return Pedido::where('revista_id', $solicitante->revista_id)
                         ->orderBy('id', 'desc')
                         ->get();
        }

        // 3. Contato (vê pedidos com serviços da sua empresa)
        if (Contato::where('user_id', $user->id)->exists()) {
            $contato = Contato::where('user_id', $user->id)->first();
            $empresa = Empresa::where('id', $contato->empresa_id)->first();
            
            // Verifica se a empresa e seus serviços existem
            if ($empresa) {
                $servicos = $empresa->servicos->pluck('id');
                return Pedido::whereHas('servicos', function ($query) use ($servicos) {
                    $query->whereIn('servicos.id', $servicos);
                })
                ->orderBy('id', 'desc')
                ->get();
            }
        }
        
        // 4. Usuário logado sem papel relevante cadastrado
        return collect(); // Retorna uma coleção vazia
    }

    /**
     * Verifica a permissão do usuário para criar um pedido.
     * Retorna o objeto Solicitante se autorizado, ou uma Response de redirecionamento.
     */
    private function handleSolicitanteGuard()
    {
        // Tenta encontrar o solicitante
        $solicitante = Solicitante::where('user_id', auth()->id())->first();

        if (!$solicitante) {
            // Se for admin, mas sem cadastro de solicitante
            if (auth()->user()->hasRole('admin')) {
                session()->flash('danger', 'Esse admin não está cadastrado como solicitante e não pode criar pedidos.');
                return redirect()->route('pedidos.index');
            }
            
            // Usuário comum sem cadastro de solicitante
            session()->flash('danger', 'Usuário não cadastrado como solicitante. Favor contatar financeiro.');
            return redirect()->route('home');
        }

        return $solicitante;
    }

    /**
     * Busca os Serviços de uma Revista e anexa o limite disponível da Licitação.
     */
    private function getServicosComLimites(Revista $revista, Licitacao $licitacao)
    {
        // 1. Pega os serviços que possuem limite para a revista e a licitação atual
        $servicos = Servico::whereHas('limites', function ($query) use ($revista, $licitacao) {
            $query->where('revista_id', $revista->id)
                  ->where('licitacao_id', $licitacao->id);
        })
        ->with(['limites' => function ($query) use ($revista, $licitacao) {
            $query->where('revista_id', $revista->id)
                  ->where('licitacao_id', $licitacao->id);
        }])
        ->get();

        // 2. Mapeia a coleção para anexar a quantidade no limite, sem modificar o objeto Servico original.
        return $servicos->map(function ($servico) {
            $limite = $servico->limites->first();
            $quantidadeDisponivel = $limite ? $limite->quantidade : 0;
            
            return (object) [
                'servico' => $servico, 
                'quantidade_disponivel' => $quantidadeDisponivel,
            ];
        });
    }

    /**
     * Sincroniza os serviços e quantidades na tabela pivot do Pedido.
     */
    private function syncServicosDoPedido(Pedido $pedido, array $validated): void
    {
        $servicosSelecionados = Servico::whereIn('id', $validated['servicos'])->get()->keyBy('id');
        $dadosParaSync = [];
        
        foreach ($validated['servicos'] as $servicoId) {
            $servico = $servicosSelecionados->get($servicoId);
            $qtd = $validated['quantidades'][$servicoId] ?? 1;

            if ($servico) {
                $dadosParaSync[$servicoId] = [
                    'quantidade' => $qtd,
                    'valor_unitario' => $servico->valor_unitario,
                ];
            }
        }

        $pedido->servicos()->sync($dadosParaSync);
    }

       /**
     * Gerencia o upload de múltiplos arquivos para o Pedido e cria os registros.
     * Retorna a contagem de arquivos salvos ou um RedirectResponse em caso de desalinhamento.
     *
     * @return int|\Illuminate\Http\RedirectResponse
     */
    private function handleArquivosDoPedido(Request $request, Pedido $pedido)
    {
        if (!$request->hasFile('arquivos')) {
            return 0;
        }

        $files = $request->file('arquivos');
        $descriptions = $request->input('descricoes', []); 
        $pedidoId = $pedido->id;

        if (count($files) !== count($descriptions)) {
            session()->flash('danger', 'Erro de submissão: O número de arquivos e descrições não é compatível. O Pedido foi criado, mas os arquivos não foram anexados. Por favor, edite o pedido e anexe novamente.');
            // Retorna um RedirectResponse para interromper o fluxo do store
            return redirect()->route('pedidos.edit', $pedido); 
        }

        $arquivos_salvos = 0;
        foreach ($files as $index => $file) {
            // A validação de 'required_with' garante que a descrição existe neste ponto.
            $description = $descriptions[$index]; 

            if ($file && $file->isValid()) {
                $fileName = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('arquivos', $fileName, 'public'); 

                Arquivo::create([
                    'pedido_id' => $pedidoId,
                    'nome' => $fileName,
                    'descricao' => $description, 
                    'caminho' => $path,
                ]);
                $arquivos_salvos++;
            }
        }
        return $arquivos_salvos;
    }
}
