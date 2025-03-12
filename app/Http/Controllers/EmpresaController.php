<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use Illuminate\Http\Request;


use function Ramsey\Uuid\v1;

class EmpresaController extends Controller
{
    //exibir o form para criar uma nova empresa
    public function create()
    {
        $this->authorize('admin');
        return view('empresas.create');
    }
    
    //armazenar a nova empresa
    public function store(Request $request)
    {
        $this->authorize('admin');
        
        $request->validate([
            'cnpj' => 'required|digits:18',
            'razao_social' => 'required',
        ]);

        $empresa = new Empresa();

        Empresa::create($empresa);
        session()->flash('success', 'Cadastrado com sucesso!');
        return redirect()->route('empresas.index');
    }

    // listar todas as empresas
    public function index()
    {
        $this->authorize('admin');
        $empresa = Empresa::all();
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
            'cnpj' => 'required|digits:18',
            'razao_social' => 'required',
        ]);
        
        $empresa->update($request);
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