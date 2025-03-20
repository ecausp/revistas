<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Empresa;
use App\Models\Contato;
use App\Models\User;

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

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->telefone = $request->telefone;
        // para contornar um usuário externo (funções do Replicado exigem um numero USP)
        $user->codpes = 0;
        $user->save();

        $contato = new Contato();
        $contato->contato_id = $user->id;
        $contato->empresa_id = $request->empresa_id;
        $contato->save();

        session()->flash('success', 'Cadastrado com sucesso!');
        return redirect()->route('empresas.index');
    }
}