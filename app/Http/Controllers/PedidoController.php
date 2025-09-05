<?php

namespace App\Http\Controllers;

use App\Mail\PedidoDevolvidoParaRevistaMail;
use App\Models\Pedido;
use App\Models\Servico;
use App\Models\Solicitante;
use App\Models\Revista;
use App\Models\Licitacao;
use App\Models\Empresa;
use App\Models\Contato;
use Illuminate\Http\Request;

use App\Mail\PedidoEnviadoParaEmpresaMail;
use Illuminate\Support\Facades\Mail;

#states
use App\States\EmElaboracao;
use App\States\EmAnaliseEmpresa;

class PedidoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        \UspTheme::activeUrl('pedidos');

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
        else
        {
            session()->flash('danger', 'Usuário não cadastrado como solicitante. Favor contatar financeiro.');
            return redirect()->route('home');

        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize("acesso-revista");
        
        $anoAtual = date('Y');
        $licitacao = Licitacao::where('ano', $anoAtual)->first();
        

        \UspTheme::activeUrl('pedidos/create');
        
        if(Solicitante::where('user_id', auth()->id())->exists())
        {
            $solicitante = Solicitante::where('user_id', auth()->user()->id)->first();
            $revista = Revista::where('id', $solicitante->revista_id)->first();
            $servicos = Servico::whereHas('limites', function ($query) use ($revista) {
                $query->where('revista_id', $revista->id);})->get();
            return view('pedidos.create', compact('solicitante','servicos', 'licitacao'));
        }
        
        else if(auth()->user()->hasRole('admin'))
        {
            session()->flash('danger', 'Esse admin não está cadastrado como solicitante e não pode criar pedidos.');
            return redirect()->route('pedidos.index');
        }
        else
        {
            session()->flash('danger', 'Usuário não cadastrado como solicitante. Favor contatar financeiro.');
            return redirect()->route('home');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize("acesso-revista");

        // Validação básica
        $validated = $request->validate([
            'solicitante_id' => 'required|exists:solicitantes,id',
            'revista_id' => 'required|exists:revistas,id',
            'licitacao_id' => 'required|exists:licitacoes,id',
            'observacoes' => 'nullable|string',
            'servicos' => 'required|array',
            'servicos.*' => 'exists:servicos,id',
            'quantidades' => 'required|array',
            'quantidades.*' => 'integer|min:1',
        ]);

        // Criando o pedido
        $pedido = Pedido::create([
            'licitacao_id' => $validated['licitacao_id'],
            'solicitante_id' => $validated['solicitante_id'],
            'revista_id' => $validated['revista_id'],
            'observacoes' => $validated['observacoes'],
        ]);

        $pedido->status = new EmElaboracao($pedido);
        
        // Relacionando serviços e quantidades
        $dadosParaSync = [];
        foreach ($validated['servicos'] as $servicoId) {
            $qtd = $validated['quantidades'][$servicoId] ?? 1;
            $dadosParaSync[$servicoId] = ['quantidade' => $qtd];
        }

        $pedido->servicos()->sync($dadosParaSync);
        
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
        

        // se for admin ou financeiro pode ver sem restrição
        if(auth()->user()->hasRole('admin') || auth()->user()->hasRole(roles: 'financeiro'))
            return view('pedidos.show', compact('pedido', 'licitacao', 'solicitante', 'servicos'));
        // Se for um solicitante de uma revista, pode visualizar os pedidos da sua revista
        else if(Solicitante::where('user_id', auth()->id())->exists())
        {
            $revista_do_pedido = $pedido->revista->id;
            $revista_do_usuario = Solicitante::where('user_id', auth()->id())->first()->revista->id;
            
            if($revista_do_usuario == $revista_do_pedido)
            {
                return view('pedidos.show', compact('pedido', 'licitacao', 'solicitante', 'servicos'));
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
                return view('pedidos.show', compact('pedido', 'licitacao', 'solicitante', 'servicos'));
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
            $servicos = Servico::whereHas('limites', function ($query) use ($revista) {
                $query->where('revista_id', $revista->id);})->get();
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

     public function paraElaboracao(Pedido $pedido)
    {
        // Troca o status
        $pedido->status->transitionTo(EmElaboracao::class);

        $pedido->save();

        foreach($pedido->solicitante as $solicitante){
            Mail::to($solicitante->user->email)->send(new PedidoDevolvidoParaRevistaMail($pedido));
        }      


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


}