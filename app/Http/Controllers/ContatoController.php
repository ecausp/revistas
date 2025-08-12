<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Empresa;
use App\Models\Contato;
use App\Models\User;

class ContatoController extends Controller
{
    public function create(Empresa $empresa)
    {
        $this->authorize('admin-financeiro');
        return view('contatos.create', compact('empresa'));
    }

    /**
     * Store a newly created resource in storage.
    **/
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'telefone' => 'required',
            'empresa_id' => 'required|exists:empresas,id',
        ]);

        // Tenta buscar um usuário pelo email
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            // Usuário não existe, cria novo
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;

            $user->codpes = 0;
            $user->save();
        }

        $user->telefone = $request->telefone;
        //dd($user->id);
        // Agora cria o contato associando a empresa com o usuário
        $contato = new Contato();
        $contato->user_id = $user->id;
        $contato->empresa_id = $request->empresa_id;
        //dd($contato);
        $contato->save();

        session()->flash('success', 'Cadastrado com sucesso!');
        return redirect()->route('empresas.index');
    }

    
   // faz o autocomplete no nome do usuário quando tenta cadastrar um novo;
    public function autocomplete(Request $request)
    {
        $query = $request->input('query');

        $users = DB::table('users')
            ->leftJoin('contatos', 'users.id', '=', 'contatos.user_id')
            ->leftJoin('empresas', 'contatos.empresa_id', '=', 'empresas.id')
            ->where(function($q) use ($query) {
                $q->where('users.name', 'LIKE', "%{$query}%")
                ->orWhere('users.email', 'LIKE', "%{$query}%");
            })
            ->select(
                'users.id as user_id',
                'users.name',
                'users.email',
                'users.telefone',
                'empresas.id as empresa_id',
                'empresas.razao_social',
                'empresas.cnpj'
            )
            ->limit(10)
            ->get();

        return response()->json($users);
    }

}