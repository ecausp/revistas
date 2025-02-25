<?php

namespace App\Http\Controllers;

use App\Models\Licitacao;
use Illuminate\Http\Request;

class LicitacaoController extends Controller
{
    //exibir o form para criar uma nova licitacao
    public function create()
    {
        return view('licitacoes.create');
    }

    //armazenar a nova licitacao
    public function store(Request $request)
    {
        $request->validate([
            'descricao' => 'required',
            'ano' => 'required|digits:4',
            'valor_total' => 'required|numeric',
        ]);

        Licitacao::create($request->all());
        return redirect()->route('licitacoes.index');
    }
}
