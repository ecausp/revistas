<?php

namespace App\Http\Controllers;

use App\Models\Contato;
use Illuminate\Http\Request;
use App\Models\Empresa;

class ContatoController extends Controller
{
    public function create(Empresa $empresa)
    {
        $this->authorize('admin');
        return view('contatos.create', compact('empresa'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('admin');
        
        $request->validate([
            'name' => 'required',
            'email' => 'required',
            'telefone' => 'required',
        ]);

        $contato = new Contato();
        $contato->name = $request->name;
        $contato->email = $request->email;
        $contato->telefone = $request->telefone;
        $contato->empresa_id = $request->empresa_id;
        $contato->save();

        session()->flash('success', 'Cadastrado com sucesso!');
        return redirect()->route('empresas.index');
    }
}