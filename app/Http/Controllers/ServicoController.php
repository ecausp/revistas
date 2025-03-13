<?php

namespace App\Http\Controllers;

use App\Models\Servico;
use Illuminate\Http\Request;

class ServicoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {   
        $this->authorize('admin');
        $servicos = Servico::all();
        return view('servicos.index', compact('servicos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('admin');
        return view('servicos.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('admin');

        // Primeiro remove os pontos
        $request['quantidade'] = str_replace('.', '', $request->quantidade);       
        // Primeiro remove os pontos e depois troca a virgula por ponto
        $request['valor_unitario'] = str_replace(',', '.', str_replace('.', '', $request->valor_unitario));

        $request->validate([
            'descricao' => 'required',
            'quantidade' => 'required|numeric',
            'unidade_medida' => 'required',
            'valor_unitario' => 'required|numeric',
        ]);

        Servico::create($request->all());
        session()->flash('success', 'Cadastrado com sucesso!');
        return redirect()->route('servicos.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Servico $servico)
    {
        $this->authorize('admin');
        return view('servicos.show', compact('servico'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Servico $servico)
    {
        $this->authorize('admin');
        return view('servicos.edit', compact('servico'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Servico $servico)
    {
        $this->authorize('admin');

        // Primeiro remove os pontos
        $request['quantidade'] = str_replace('.', '', $request->quantidade);       
        // Primeiro remove os pontos e depois troca a virgula por ponto
        $request['valor_unitario'] = str_replace(',', '.', str_replace('.', '', $request->valor_unitario));

        $request->validate([
            'descricao' => 'required',
            'quantidade' => 'required|numeric',
            'unidade_medida' => 'required',
            'valor_unitario' => 'required|numeric',
        ]);
        
        $servico->update($request->all());
        session()->flash('success', 'Atualizado com sucesso!');
        return redirect()->route('servicos.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Servico $servico)
    {
        $this->authorize('admin');
        $servico->delete();
        session()->flash('danger', 'Serviço: ' . $servico->descricao . ' removido!');
        return redirect()->route('servicos.index');
    }
}