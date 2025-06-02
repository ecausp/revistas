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
        $licitacoes = Licitacao::where('ano', $anoAtual)->get();

        $solicitante = Solicitante::where('user_id', auth()->user()->id)->first();
        $servicos = Servico::all();
        \UspTheme::activeUrl('pedidos/create');

        return view('pedidos.create', compact('solicitante','servicos', 'licitacoes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
            dd($request->all()); // aqui vai mostrar tudo que chegou
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