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
        if(auth()->user()->hasRole('admin') || auth()->user()->hasRole(roles: 'financeiro'))
        {
            return view('solicitantes.create', compact('revista'));
        }
        else
        {
            session()->flash('error', 'Usuário não autorizado a acessar esse recurso');
            return redirect()->route('home');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $this->authorize('admin-financeiro');
        $solicitante = new Solicitante();
        $solicitante->user_id = $request->user_id;
        $solicitante->revista_id = $request->revista_id;
        $solicitante->save();

        $user = User::where('id', $request->user_id)->first();
        $user->givePermissionTo(['revistas.revista']);
        $user->save();

        session()->flash('success', 'Cadastrado com sucesso!');
        return redirect()->route('revistas.index');
    }


public function buscarUsuario(Request $request)
{
    $nome = $request->get('nome');
    $revistaId = $request->get('revista_id');

    if (!$nome) {
        return response()->json([]);
    }

    $usuarios = User::whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($nome) . '%'])
        ->limit(10)
        ->get();

    $resposta = $usuarios->map(function ($usuario) use ($revistaId) {
        $jaCadastrado = false;

        if ($revistaId) {
            $jaCadastrado = Solicitante::where('user_id', $usuario->id)
                ->where('revista_id', $revistaId)
                ->exists();
        }

        return [
            'id' => $usuario->id,
            'name' => $usuario->name,
            'email' => $usuario->email,
            'codpes' => $usuario->codpes ?? '',
            'ja_cadastrado' => $jaCadastrado,
        ];  
    });

    return response()->json($resposta);
}
}