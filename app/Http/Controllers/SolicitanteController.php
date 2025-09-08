<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Revista;
use App\Models\Solicitante;
use App\Models\User;

use App\Util\ReplicadoTemp;


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


        // Tenta buscar um usuário pelo email
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            // Usuário não existe, cria novo
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->telefone = 0;
            $user->codpes = $request->codpes;
            $user->save();
        }

        $user->givePermissionTo(['revistas.revista']);
        $user->save();

        $solicitante = new Solicitante();
        $solicitante->user_id =  $user->id; 
        $solicitante->revista_id = $request->revista_id;
        $solicitante->save();


        session()->flash('success', 'Cadastrado com sucesso!');
        return redirect()->route('revistas.index');
    }


    public function buscarUsuario(Request $request)
    {
        $nome = $request->query('nome', '');

        logger()->debug('step1', ['nome' => $nome]);

        // Pega a lista de servidores do ReplicadoTemp
        $servidores = ReplicadoTemp::listarServidoresDocentes();


        logger()->debug('step2', [
            'servidores_count' => count($servidores),
            'first' => $servidores[0] ?? null
        ]);

        $matches = array_filter($servidores, function ($pessoa) use ($nome) {
            return stripos($pessoa['nompesttd'], $nome) !== false;
        });

        logger()->debug('step3', [
            'matched' => count($matches),
            'sample' => array_slice($matches, 0, 5)
        ]);

        $result = array_map(function ($pessoa) {
            return [
                'id' => $pessoa['codpes'],
                'name' => $pessoa['nompesttd'],
                'codpes' => $pessoa['codpes'],
                'tipvin' => $pessoa['tipvin'],
                'email' => $pessoa['codema'],
                'numseqpes' => $pessoa['numseqpes'],
                'display' => $pessoa['nompesttd'] . ' (' . ($pessoa['tipvin'] ?? '') . ')',
                'ja_cadastrado' => false
            ];
        }, $matches);

        return response()->json(array_values($result));
    }




}