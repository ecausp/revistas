<?php

namespace App\Http\Controllers;

use App\Models\Licitacao;
use App\Models\User;

use Illuminate\Http\Request;

class LicitacaoController extends Controller
{
    //exibir o form para criar uma nova licitacao
    public function create()
    {
        $this->authorize("admin-revista");
        \UspTheme::activeUrl('licitacoes/create');

        return view('licitacoes.create');
    }

    //armazenar a nova licitacao
    public function store(Request $request)
    {
        $this->authorize("admin-revista");
        
        // Primeiro remove os pontos e depois troca a virgula por ponto
        $request['valor_total'] = str_replace(',', '.', str_replace('.', '', $request->valor_total));
        
        $request->validate([
            'descricao' => 'required',
            'ano' => 'required|digits:4',
            'valor_total' => 'required|numeric',
        ]);

        $licitacao = new Licitacao();
        $licitacao = [
            'editor' => $request->user()->id,
            "descricao" => $request->descricao,
            "ano" => $request->ano,
            "valor_total" => $request->valor_total,
        ];

        Licitacao::create($licitacao);

        session()->flash('success', 'Cadastrado com sucesso!');
        return redirect()->route('licitacoes.index');
    }

    // listar todas as licitacoes
    public function index()
    {
        $this->authorize("admin-revista");
        \UspTheme::activeUrl('licitacoes');

        $licitacoes = Licitacao::orderBy('ano', 'desc')->orderBy('descricao')->get();

        return view('licitacoes.index', compact('licitacoes'));
    }

    // Exibir os detalhes de uma licitação
    public function show(Licitacao $licitacao)
    {
        $this->authorize("admin-revista");

        $editor = User::where('id', $licitacao->editor)->first();
        $updated_at = $licitacao->updated_at;

        return view('licitacoes.show', compact("licitacao", 'updated_at', "editor"));
    }

    // Exibir o formulário para editar uma licitação
    public function edit(Licitacao $licitacao)
    {
        $this->authorize("admin-revista");

        return view('licitacoes.edit', compact('licitacao'));
    }

    // Atualizar os dados de uma licitação
    public function update(Request $request, Licitacao $licitacao)
    {
        $this->authorize("admin-revista");

        // Primeiro remove os pontos e depois troca a virgula por ponto
        $request['valor_total'] = str_replace(',', '.', str_replace('.', '', $request->valor_total));
        
        $request->validate([
            'descricao' => 'required',
            'ano' => 'required|digits:4',
            'valor_total' => 'required|numeric',
        ]);

        $licitacao_editada = [
            'editor' => $request->user()->id,
            "descricao" => $request->descricao,
            "ano" => $request->ano,
            "valor_total" => $request->valor_total,
        ];
        
        $licitacao->update($licitacao_editada);

        session()->flash('success', 'Atualizado com sucesso!');
        return redirect()->route('licitacoes.index');
    }

    public function destroy(Licitacao $licitacao)
    {
        $this->authorize("admin-revista");

        $licitacao->delete();
        
        session()->flash('danger', 'Licitação: ' . $licitacao->descricao . ' - ' . $licitacao->ano . ' removida!');
        return redirect()->route('licitacoes.index');
    }
}