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
        $this->authorize("admin-financeiro");
        \UspTheme::activeUrl('pedidos');

        $pedidos = Pedido::all();
        
        return view('pedidos.index', compact('pedidos'));
    
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize("admin-financeiro");
        
        $anoAtual = date('Y');
        $licitacao = Licitacao::where('ano', $anoAtual)->first();

        $solicitante = Solicitante::where('user_id', auth()->user()->id)->first();
        $servicos = Servico::all();
        \UspTheme::activeUrl('pedidos/create');

        return view('pedidos.create', compact('solicitante','servicos', 'licitacao'));
    }

    /**
     * Store a newly created resource in storage.
     */
public function store(Request $request)
{
    $this->authorize("admin-financeiro");

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

    session()->flash('success', 'Pedido cadastrado com sucesso!');
    return redirect()->route('pedidos.index');
}


    /**
     * Display the specified resource.
     */
    public function show(Pedido $pedido)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pedido $pedido)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Pedido $pedido)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pedido $pedido)
    {
        //
    }
}