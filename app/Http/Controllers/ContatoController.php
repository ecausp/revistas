<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Empresa;
use App\Models\Contato;
use App\Models\User;
use Illuminate\Support\Facades\DB;
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
        $contato->user_id = $user->id;
        $contato->empresa_id = $request->empresa_id;
        $contato->save();

        session()->flash('success', 'Cadastrado com sucesso!');
        return redirect()->route('empresas.index');
    }

    public function index()
    {
        return view('searchDemo');
    }
    
   
public function autocomplete(Request $request)
{
    $query = $request->input('query');

    $users = DB::table('users')
        ->leftJoin('contatos', 'users.id', '=', 'contatos.user_id')
        ->leftJoin('empresas', 'contatos.empresa_id', '=', 'empresas.id')
        ->where('users.name', 'LIKE', "%{$query}%")
        ->orWhere('users.email', 'LIKE', "%{$query}%")
        ->select(
            'users.id as user_id',
            'users.name',
            'users.email',
            'users.telefone',
            'empresas.id as empresa_id',
            'empresas.razao_social',
            'empresas.cnpj'
        )
        ->get();

    return response()->json($users);
}

    


    // public function autocomplete(Request $request)
    // {
    //     $data = User::select( "name", "email", "telefone") // Pegando os dados necessários
    //                 ->where('name', 'LIKE', '%'. $request->get('query'). '%')
    //                 ->take(10)
    //                 ->get();
    
    //     // Retornar os dados como um array associativo para que o frontend possa processá-los corretamente
    //     return response()->json($data);
    // }
    
 

}