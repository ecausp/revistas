<?php

namespace App\Http\Controllers;

use App\Models\Licitacao;
use Illuminate\Http\Request;

class LicitacaoController extends Controller
{
    //exibir o form para criar uma nova licitacao
    public function create()
    {
        return view('licitacoes.create');
    }

    //armazenar a nova licitacao
    public function store(Request $request)
    {
        $request->validate([
            'descricao' => 'required',
            'ano' => 'required|digits:4',
            'valor_total' => 'required|numeric',
        ]);

        Licitacao::create($request->all());
        return redirect()->route('licitacoes.index');
    }

    // listar todas as licitacoes
    public function index()
    {
        $licitacoes = Licitacao::all();
        return view('licitacoes.index', compact('licitacoes'));
    }

    // Exibir os detalhes de uma licitação
    public function show(Licitacao $licitacao)
    {
        return view('licitacoes.show', compact('licitacao'));
    }

    // Exibir o formulário para editar uma licitação
    public function edit(Licitacao $licitacao)
    {
        return view('licitacoes.edit', compact('licitacao'));
    }

    // Atualizar os dados de uma licitação
    public function update(Request $request, Licitacao $licitacao)
    {
        $request->validate([
            'descricao' => 'required',
            'ano' => 'required|digits:4',
            'valor_total' => 'required|numeric',
        ]);

        $licitacao->update($request->all());
        return redirect()->route('licitacoes.index');
    }

    public function destroy(Licitacao $licitacao)
    {
        $licitacao->delete();
        return redirect()->route('licitacoes.index');
    }
}
