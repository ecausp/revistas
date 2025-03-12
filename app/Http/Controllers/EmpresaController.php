<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
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
        ]);

        $empresa = new Empresa();

        Empresa::create($request->all());
        session()->flash('success', 'Cadastrado com sucesso!');
        return redirect()->route('empresas.index');
    }

    // listar todas as empresas
    public function index()
    {
        $this->authorize('admin');
        $empresas = Empresa::all();
        return view('empresas.index', compact('empresas'));
    }
    
    // Exibir os detalhes de uma empresa
    public function show(Empresa $empresa)
    {
        $this->authorize('admin');
        return view('empresas.show', compact('empresa'));
    }
    
    // Exibir o formulário para editar uma empresa
    public function edit(Empresa $empresa)
    {
        $this->authorize('admin');
        return view('empresas.edit', compact('empresa'));
    }
    
    // Atualizar os dados de uma empresa
    public function update(Request $request, Empresa $empresa)
    {
        $this->authorize('admin');
    
        $request->validate([
            'cnpj' => 'required',
            'razao_social' => 'required',
        ]);
        
        $empresa->update($request->all());
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