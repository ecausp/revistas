<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Revista;
use App\Models\Solicitante;
use App\Models\User;

class SolicitanteController extends Controller
{
    public function create(Revista $revista)
    {
        return view('solicitantes.create', compact('revista'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $solicitante = new Solicitante();
        $solicitante->user_id = $request->user_id;
        $solicitante->revista_id = $request->revista_id;
        $solicitante->save();

        session()->flash('success', 'Cadastrado com sucesso!');
        return redirect()->route('revistas.index');
    }
}