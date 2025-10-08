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

use Illuminate\Support\Facades\Storage;

use App\States\EmExecucao;
use Illuminate\Http\Request;

use App\Mail\PedidoDevolvidoParaRevistaMail;
use App\Mail\PedidoEnviadoParaEmpresaMail;
use App\Mail\PedidoEnviadoParaFinanceiroMail;
use App\Mail\PedidoEnviadoParaExecucaoMail;
use Illuminate\Support\Facades\Mail;

#states
use App\States\EmElaboracao;
use App\States\EmAnaliseEmpresa;
use App\States\EmAnaliseFinanceiro;

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
        if ($solicitante instanceof \Illuminate\Http\RedirectResponse) {
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

        // 1. Regras de validação (Ainda no Controller)
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
        
        // 3. SINCRONIZAÇÃO DE SERVIÇOS (Função auxiliar)
        $this->syncServicosDoPedido($pedido, $validated);

        // 4. UPLOAD DE ARQUIVOS (Função auxiliar)
        $resultadoArquivos = $this->handleArquivosDoPedido($request, $pedido);

        // Verifica se a função de arquivo retornou um RedirectResponse
        if ($resultadoArquivos instanceof \Illuminate\Http\RedirectResponse) {
            return $resultadoArquivos;
        }
        $arquivos_salvos = $resultadoArquivos;
        
        // 5. HISTÓRICO E REDIRECIONAMENTO FINAL
        $pedido->adicionarHistorico($pedido->status);

        // (Seu bloco final de redirecionamento idêntico à função index, que deve ser refatorado futuramente)
        if(Solicitante::where('user_id', auth()->id())->exists()|| auth()->user()->hasRole('admin'))
        {
            session()->flash('success', 'Pedido cadastrado com sucesso!');
            return redirect()->route('pedidos.index');
        }
        else
        {
            session()->flash('danger', 'Usuário não cadastrado como solicitante. Favor contatar financeiro.');
            return redirect()->route('home');
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
        // Obtém a licitação para usar no filtro de limite
        $licitacao = $pedido->licitacao;

        // se o pedido já foi enviado para a empresa, ele não pode ser editado;
        if($pedido->status == "em_analise_empresa")
        {
            session()->flash('danger', 'Pedido em análise pela empresa, não pode ser editado.');
            return redirect()->route('home');
        }

        // se o usuário é solicitante, só pode editar pedidos da revista dele 
        else if(Solicitante::where('user_id', auth()->id())->exists() && $pedido->revista->id == Solicitante::where('user_id', auth()->id())->first()->revista->id)
        {  
            $solicitante = Solicitante::where('user_id', auth()->user()->id)->first();
            $revista = Revista::where('id', $solicitante->revista_id)->first();
        
            // 1. Carregar explicitamente a relação 'arquivos'
            $pedido->load('arquivos'); 

            // 2. Carregar serviços e ATTACH o limite disponível (igual ao create)
            $servicos = Servico::whereHas('limites', function ($query) use ($revista, $licitacao) {
                $query->where('revista_id', $revista->id)
                    ->where('licitacao_id', $licitacao->id);})
                    ->with(['limites' => function ($query) use ($revista, $licitacao) {
                        $query->where('revista_id', $revista->id)
                            ->where('licitacao_id', $licitacao->id);}])
                                ->get();
        
            // 3. Anexar a quantidade_disponivel e pivot_quantidade para uso no Blade/JS
            foreach ($servicos as $servico) {
                $limite = $servico->limites->first();
                $servico->quantidade_disponivel = $limite ? $limite->quantidade : 0;
            }

            return view('pedidos.edit', compact('pedido', 'servicos'));
        }
        
        else
        {
            session()->flash('danger', 'Esse usuário não pode editar esse pedido.');
            return redirect()->route('pedidos.index');
        }
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
        ]);
        // Atualiza dados simples
        $pedido->observacoes = $request->observacoes;
        $pedido->revista_id = $request->revista_id;
        $pedido->solicitante_id = $request->solicitante_id;
        $pedido->licitacao_id = $request->licitacao_id;
        $pedido->save();

        // Sincroniza os serviços com a tabela pivot e quantidades
        $syncData = [];

        foreach ($request->servicos ?? [] as $id) {
            $syncData[$id] = ['quantidade' => $request->quantidades[$id] ?? 1];
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
            $syncData[$servicoId] = ['quantidade' => $quantidades[$servicoId] ?? 1];
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

        $licitacao = $pedido->licitacao;
        $revista = $pedido->revista;
        $saldos = $revista->saldos;
        $total = $pedido->valorTotalPedido();



        foreach($saldos as $saldo)
        {
            if($saldo->licitacao_id == $licitacao->id)
            {
                $saldo->valor_contratado = $saldo->valor_contradado + $total;
                $saldo->save(); 
            }
        }

        // Troca o status
        $pedido->status->transitionTo(EmExecucao::class);
        $pedido->save();

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

        $pedido->adicionarHistorico($pedido->status, "Empenho efetuado");

        session()->flash('success', 'Pedido enviado para execução.');


        // se o pedido foi empenhado, precisa atualizar o saldo das revistas que fizeram esse pedido
        


        
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


    /**
     * Retorna a coleção de Pedidos que o usuário logado tem permissão para visualizar.
     */
    private function getPedidosDoUsuario()
    {
        $user = auth()->user();

        // 1. Admin ou Financeiro (vê todos)
        if ($user->hasRole('admin') || $user->hasRole('financeiro')) {
            return Pedido::all();
        }

        // 2. Solicitante (vê pedidos da sua revista)
        if (Solicitante::where('user_id', $user->id)->exists()) {
            $solicitante = Solicitante::where('user_id', $user->id)->first();
            return Pedido::where('revista_id', $solicitante->revista_id)->get();
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
                })->get();
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
     * Retorna a contagem de arquivos salvos. Redireciona em caso de desalinhamento.
     */
    private function handleArquivosDoPedido(Request $request, Pedido $pedido)
    {
        if (!$request->hasFile('arquivos')) {
            return 0;
        }

        $files = $request->file('arquivos');
        $descriptions = $request->input('descricoes', []); 
        $pedidoId = $pedido->id;

        $arquivos_salvos = 0;
        foreach ($files as $index => $file) {
            if ($file && $file->isValid()) {
                $description = $descriptions[$index]; 
                
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