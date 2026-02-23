<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Revista;
use App\Models\Solicitante;
use App\Models\User;

use App\Util\ReplicadoTemp;

use Spatie\Permission\Models\Permission;


class SolicitanteController extends Controller
{
    public function create(Revista $revista)
    {
        $user = auth()->user();
        $solicitante = Solicitante::Where('user_id', $user->id)->first();
        if($user->hasAnyRole(['admin','financeiro']) || $solicitante != null && $solicitante->revista_id == $revista->id)
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

        $user->givePermissionTo(
            Permission::where('guard_name', User::$hierarquiaNs)->where('name', 'user')->first()
        );

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
        $revista_id = $request->query('revista_id'); // pega o id da revista enviado pelo frontend

        // Pega a lista de servidores do ReplicadoTemp
        $servidores = $this->listarServidoresDocentes();

        // Filtra servidores pelo nome
        $matches = array_filter($servidores, function ($pessoa) use ($nome) {
            return stripos($pessoa['nompesttd'], $nome) !== false;
        });

        // Monta o resultado
        $result = array_map(function ($pessoa) use ($revista_id) {

            // Verifica se já existe um solicitante para essa revista com esse codpes
            $jaCadastrado = Solicitante::where('revista_id', $revista_id)
                ->whereHas('user', function($q) use ($pessoa) {
                    $q->where('codpes', $pessoa['codpes']);
                })
                ->exists();

            return [
                'id' => $pessoa['codpes'],
                'name' => $pessoa['nompesttd'],
                'codpes' => $pessoa['codpes'],
                'tipvin' => $pessoa['tipvin'],
                'email' => $pessoa['codema'],
                'numseqpes' => $pessoa['numseqpes'],
                'display' => $pessoa['nompesttd'] . ' (' . ($pessoa['tipvin'] ?? '') . ')',
                'ja_cadastrado' => $jaCadastrado
            ];
        }, $matches);

        return response()->json(array_values($result));
    }

    protected function listarServidoresDocentes()
    {
        return ReplicadoTemp::listarServidoresDocentes();
    }


}