<?php

namespace App\Http\Controllers;

use App\Models\Licitacao;
use App\Models\Empresa;
use App\Models\User;
use App\Models\Solicitante;
use App\Models\Saldo;

use Illuminate\Http\Request;

class LicitacaoController extends Controller
{
    //exibir o form para criar uma nova licitacao
    public function create()
    {
        $this->authorize("admin-financeiro");
        \UspTheme::activeUrl('licitacoes/create');

        return view('licitacoes.create');
    }

    //armazenar a nova licitacao
    public function store(Request $request)
    {
        $this->authorize("admin-financeiro");
        
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
        \UspTheme::activeUrl('licitacoes');

        $licitacoes = Licitacao::orderBy('ano', 'desc')->orderBy('descricao')->get();
        $empresas = Empresa::get();
        return view('licitacoes.index', compact('licitacoes', 'empresas'));
    }

    // Exibir os detalhes de uma licitação
    public function show(Licitacao $licitacao)
    {
        $saldos = NULL;
        $editor = User::where('id', $licitacao->editor)->first();
        $updated_at = $licitacao->updated_at;
        if(auth()->user()->hasRole(roles: 'financeiro'))
        {
            $saldos = Saldo::all();
        }
        // verificar se é user revista e qual revista ele faz parte pra ver só a sua revista;
        else if(Solicitante::where('user_id', auth()->id())->exists())
        {

            $solicitante = Solicitante::where('user_id', auth()->id())->first();
            $saldos = Saldo::where('revista_id', $solicitante->revista_id)->get();
        }
        return view('licitacoes.show', compact("licitacao", 'updated_at', "editor", 'saldos'));
    }

    // Exibir o formulário para editar uma licitação
    public function edit(Licitacao $licitacao)
    {
        $this->authorize("admin-financeiro");

        return view('licitacoes.edit', compact('licitacao'));
    }

    // Atualizar os dados de uma licitação
    public function update(Request $request, Licitacao $licitacao)
    {
        $this->authorize("admin-financeiro");

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
        $this->authorize("admin-financeiro");

        $licitacao->delete();
        
        session()->flash('danger', 'Licitação: ' . $licitacao->descricao . ' - ' . $licitacao->ano . ' removida!');
        return redirect()->route('licitacoes.index');
    }

    public function addEmpresas(Request $request, Licitacao $licitacao)
    {
        // Validação básica
        $request->validate([
            'empresas' => 'required|array',          // deve ser um array
            'empresas.*' => 'exists:empresas,id',    // cada ID precisa existir na tabela empresas
        ]);

        // Vincula as empresas selecionadas à licitação
        // syncWithoutDetaching evita remover empresas já vinculadas
        $licitacao->empresas()->syncWithoutDetaching($request->empresas);

        // Redireciona de volta com mensagem de sucesso
        session()->flash('success', 'Atualizado com sucesso!');
        return redirect()->route('licitacoes.index');
    }

}