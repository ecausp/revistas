<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\Contato;
use App\Models\User;
use App\Models\EmpresaLicitacao;
use App\Models\Licitacao;
use Illuminate\Http\Request;

use function Ramsey\Uuid\v1;

class EmpresaController extends Controller
{
    //exibir o form para criar uma nova empresa
    public function create()
    {
        $this->authorize('admin');
        
        $licitacoes = Licitacao::orderBy('ano', 'desc')->orderBy('descricao')->get();
        return view('empresas.create', compact('licitacoes'));
    }
    
    //armazenar a nova empresa
    public function store(Request $request)
    {
        $this->authorize('admin');
        
        $request->validate([
            'cnpj' => 'required',
            'razao_social' => 'required',
            'licitacao_id' => 'required',
        ]);

        $empresa = new Empresa();
        $empresa->editor = $request->user()->id;
        $empresa->cnpj = $request->cnpj;
        $empresa->razao_social = $request->razao_social;
        $empresa->save();

        $relacao = new EmpresaLicitacao();
        $relacao->empresa_id = $empresa->id;
        $relacao->licitacao_id = $request->licitacao_id;
        $relacao->valor = 1000;
        $relacao->save();

        session()->flash('success', 'Cadastrado com sucesso!');
        return redirect()->route('empresas.index');
    }

    // listar todas as empresas
    public function index()
    {
        $this->authorize('admin');
        $empresas = Empresa::orderBy('updated_at', 'desc')->get();
        return view('empresas.index', compact('empresas'));
    }
    
    // Exibir os detalhes de uma empresa
    public function show(Empresa $empresa)
    {

        $this->authorize('admin');
        $editor = User::where('id', $empresa->editor)->first();
        $updated_at = $empresa->updated_at;
        $contatos = Contato::where('empresa_id', $empresa->id)->get();
        return view('empresas.show', compact('empresa', 'contatos', "editor", "updated_at"));
    }
    
    // Exibir o formulário para editar uma empresa
    public function edit(Empresa $empresa)
    {
        $this->authorize('admin');
        $licitacoes = Licitacao::orderBy('ano', 'desc')->orderBy('descricao')->get();
        return view('empresas.edit', compact('empresa', 'licitacoes'));
    }
    
    // Atualizar os dados de uma empresa
    public function update(Request $request, Empresa $empresa)
    {        
        $this->authorize('admin');
    
        $request->validate([
            'cnpj' => 'required',
            'razao_social' => 'required',
        ]);

        $empresa_editada = [
            'editor' => $request->user()->id,
            "cnpj" => $request->cnpj,
            "razao_social" => $request->razao_social,
        ];


        $empresa->update($empresa_editada);
        session()->flash('success', 'Atualizado com sucesso!');
        return redirect()->route('empresas.index');
    }
    
    public function destroy(Empresa $empresa)
    {
        $this->authorize('admin');
        $empresa->delete();
        session()->flash('danger', 'Empresa: ' . $empresa->cnpj . ' - ' . $empresa->razao_social . ' removida!');
        return redirect()->route('empresas.index');
        }
    
}