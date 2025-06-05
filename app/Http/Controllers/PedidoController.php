<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\Servico;
use App\Models\Solicitante;
use App\Models\Licitacao;
use Illuminate\Http\Request;


class PedidoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize("revista");
        \UspTheme::activeUrl('pedidos');

        $pedidos = Pedido::all();
        
        if(Solicitante::where('user_id', auth()->id())->exists())
            return view('pedidos.index', compact('pedidos'));
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
        $this->authorize("revista");
        
        $anoAtual = date('Y');
        $licitacao = Licitacao::where('ano', $anoAtual)->first();
        
        $solicitante = Solicitante::where('user_id', auth()->user()->id)->first();
        $servicos = Servico::all();
        \UspTheme::activeUrl('pedidos/create');
        
        if(Solicitante::where('user_id', auth()->id())->exists())
            return view('pedidos.create', compact('solicitante','servicos', 'licitacao'));
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
    $this->authorize("revista");

    // Validação básica
    $validated = $request->validate([
        'solicitante_id' => 'required|exists:solicitantes,id',
        'revista_id' => 'required|exists:revistas,id',
        'licitacao_id' => 'required|exists:licitacoes,id',
        'observacoes' => 'nullable|string',
        'servicos' => 'required|array',
        'servicos.*' => 'exists:servicos,id',
        'quantidades' => 'required|array',
        'quantidades.*' => 'numeric|min:1',
    ]);

    // Criando o pedido
    $pedido = Pedido::create([
        'licitacao_id' => $validated['licitacao_id'],
        'solicitante_id' => $validated['solicitante_id'],
        'revista_id' => $validated['revista_id'],
        'observacoes' => $validated['observacoes'],
    ]);

    // Relacionando serviços e quantidades
    $dadosParaSync = [];
    foreach ($validated['servicos'] as $servicoId) {
        $qtd = $validated['quantidades'][$servicoId] ?? 1;
        $dadosParaSync[$servicoId] = ['quantidade' => $qtd];
    }

    $pedido->servicos()->sync($dadosParaSync);
    
    if(Solicitante::where('user_id', auth()->id())->exists())
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
        $this->authorize("revista");
        $licitacao = $pedido->licitacao;
        $solicitante = $pedido->solicitante;
        
        if(Solicitante::where('user_id', auth()->id())->exists())
            return view('pedidos.show', compact('pedido', 'licitacao', 'solicitante'));
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
        $servicos = Servico::all();
        if(Solicitante::where('user_id', auth()->id())->exists())
            return view('pedidos.edit', compact('pedido', 'servicos'));

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
    if(Solicitante::where('user_id', auth()->id())->exists())
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
        $this->authorize("revista");

        $pedido->delete();
        
        if(Solicitante::where('user_id', auth()->id())->exists())        
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
}