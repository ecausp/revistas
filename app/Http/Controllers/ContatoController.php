<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
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

        User::create($request->all());
        session()->flash('success', 'Cadastrado com sucesso!');
        return redirect()->route('empresas.index');
    }
}