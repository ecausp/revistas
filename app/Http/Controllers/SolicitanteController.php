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
        $this->authorize('admin');
        return view('solicitantes.create', compact('revista'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // tbd;
    }
    
   // faz o autocomplete no nome do usuário quando tenta cadastrar um novo;
    public function autocomplete(Request $request)
    {
        $query = $request->input('query');

        $users = DB::table('users')
            ->leftJoin('solicitantes', 'users.id', '=', 'solicitantes.user_id')
            ->leftJoin('revistas', 'solicitantes.revista_id', '=', 'revistas.id')
            ->where('users.name', 'LIKE', "%{$query}%")
            ->orWhere('users.email', 'LIKE', "%{$query}%")
            ->select(
            'users.id as user_id',
                'users.name',
                'users.email',
                'users.telefone',
                'revistas.id as revista_id',
                'revistas.nome'
            )->get();

    return response()->json($users);
}
}