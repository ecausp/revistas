<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\Servico;
use App\Models\Solicitante;
use App\Models\Revista;
use App\Models\Licitacao;
use App\Models\User;
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
        $this->authorize("acesso-revista");
        \UspTheme::activeUrl('pedidos');

        if(auth()->user()->hasRole('admin') || auth()->user()->hasRole(roles: 'financeiro'))
        {
            $pedidos = Pedido::all(); 
            return view('pedidos.index', compact('pedidos'));
        }
        else if(Solicitante::where('user_id', auth()->id())->exists())
        {   
            $solicitante = Solicitante::where('user_id', auth()->id())->first();
            $pedidos = Pedido::where('revista_id', $solicitante->revista_id)->get();  
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
        $this->authorize("acesso-revista");
        $licitacao = $pedido->licitacao;
        $solicitante = $pedido->solicitante;
        $servicos = Servico::all();

        if(Solicitante::where('user_id', auth()->id())->exists()|| auth()->user()->hasRole('admin') || auth()->user()->hasRole(roles: 'financeiro'))
            return view('pedidos.show', compact('pedido', 'licitacao', 'solicitante', 'servicos'));
        else
        {
            session()->flash('danger', 'Usuário não cadastrado como solicitante. Favor contatar financeiro.');
            return redirect()->route('home');

        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pedido $pedido)
    {
        $this->authorize("acesso-revista");

        if(Solicitante::where('user_id', auth()->id())->exists())
        {
            $solicitante = Solicitante::where('user_id', auth()->user()->id)->first();
            $revista = Revista::where('id', $solicitante->revista_id)->first();
            $servicos = Servico::whereHas('limites', function ($query) use ($revista) {
                $query->where('revista_id', $revista->id);})->get();
            return view('pedidos.edit', compact('pedido', 'servicos'));
        }
        else if(auth()->user()->hasRole('admin'))
        {
            session()->flash('danger', 'Esse admin não está cadastrado como solicitante e não pode editar esse pedido.');
            return redirect()->route('pedidos.index');
        }


        else
        {
            session()->flash('danger', 'Usuário não cadastrado como solicitante. Favor contatar financeiro.');
            return redirect()->route('home');

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


        session()->flash('error', 'Pedido devolvido para elaboração.');
        
        $solicitante = Solicitante::where('user_id', auth()->id())->first();
        $pedidos = Pedido::where('revista_id', $solicitante->revista_id)->get();  
        return view('pedidos.index', compact('pedidos'));
    }

    public function paraAnaliseEmpresa(Pedido $pedido)
    {
        // 1. Atualiza o status do pedido
        $pedido->status->transitionTo(EmAnaliseEmpresa::class);
        $pedido->save();

        // 2. Envia o e-mail para a empresa
        Mail::to("jpedrotoledo@usp.br")->send(new PedidoEnviadoParaEmpresaMail($pedido));
        

        session()->flash('success', 'Pedido enviado por e-mail para os contatos da empresa responsável.');
        
        $solicitante = Solicitante::where('user_id', auth()->id())->first();
        $pedidos = Pedido::where('revista_id', $solicitante->revista_id)->get();  
        
        return view('pedidos.index', compact('pedidos'));
    }

}