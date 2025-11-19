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

        return redirect('/ajuda');
    }

    public function index(){
        $ajudas = Ajuda::all();
        return view('ajuda.index', compact('ajudas'));
    }

    public function create()
    {
        return view('ajuda.create', ['ajuda' => new Ajuda()]);
    }

    public function edit(Ajuda $ajuda)
    {
        return view('ajuda.edit', compact('ajuda'));
    }

    public function update(Request $request, Ajuda $ajuda)
    {
        $dados = $request->validate([
            'categoria' => 'required',
            'descricao' => 'required',
            'url' => 'required'
        ]);

        $ajuda->update($dados);

        return redirect()->route('ajuda.index');
    }

    public function destroy(Ajuda $ajuda)
    {
        $ajuda->delete();
        return redirect()->route('ajuda.index');
    }
}
