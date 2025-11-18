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
}
