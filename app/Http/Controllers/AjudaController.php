<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Ajuda;
use App\Enums\CategoriaAjuda;

class AjudaController extends Controller
{
    public function store(Request $request) {
        $dados = $request->validate([
            'categoria' => 'required',
            'descricao' => 'required',
            'url' => 'required'
        ]);

        Ajuda::create($dados);

        return redirect('/ajudas');
    }

    public function index(){
        $ajudas = Ajuda::all();
        return view('ajuda.index', compact('ajudas'));
    }

    public function create()
    {
        return view('ajuda.create');
    }
}
