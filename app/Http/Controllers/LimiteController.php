<?php

namespace App\Http\Controllers;

use App\Models\Licitacao;
use App\Models\Limite;
use App\Models\Revista;
use App\Models\Servico;
use Illuminate\Http\Request;

class LimiteController extends Controller
{
    //lista os Limites
    public function index()
    {
        $this->authorize('admin');
        \UspTheme::activeUrl('limites');
        
        $limites = Limite::all();
        
        return view('limites.index', compact('limites'));
    }

    public function create()
    {
        $this->authorize('admin');
        \UspTheme::activeUrl('limites/create');

        $licitacoes = Licitacao::all();
        $revistas = Revista::all();
        $servicos = Servico::all();

        return view('limites.create', compact('licitacoes', 'revistas', 'servicos'));
    }

    public function store(Request $request)
    {
        $this->authorize('admin');

        $request->validate([
            'licitacao_id' => 'required|exists:licitacoes,id',
            'revista_id' => 'required|exists:revistas,id',
            'servico_id' => 'required|exists:servicos,id',
            'quantidade' => 'required|integer|min:1',
            'valor_unitario' => 'required|numeric|min:0',
        ]);

        Limite::create($request->all());

        session()->flash('success', 'Cadastrado com sucesso!');
        return redirect()->route('limites.index');
    }

    public function show(Limite $limite)
    {
        $this->authorize('admin');

        return view('limites.show', compact('limite'));
    }

    public function edit(Limite $limite)
    {
        $this->authorize('admin');
        
        $licitacoes = Licitacao::all();
        $revistas = Revista::all();
        $servicos = Servico::all();

        return view('limites.edit', compact('limite','licitacoes', 'revistas', 'servicos'));
    }

    public function update(Request $request, Limite $limite)
    {
        $this->authorize('admin');

        $request->validate([
            'licitacao_id' => 'required|exists:licitacoes,id',
            'revista_id' => 'required|exists:revistas,id',
            'servico_id' => 'required|exists:servicos,id',
            'quantidade' => 'required|integer|min:1',
            'valor_unitario' => 'required|numeric|min:0',
        ]);
        $limite->update($request->all());

        session()->flash('success', 'Atualizado com sucesso!');
        return redirect()->route('limites.index');
    }

    public function destroy(Limite $limite)
    {
        $this->authorize('admin');

        $limite->delete();

        session()->flash('danger', 'Limite: ' . $limite->id . ' removida!');
        return redirect()->route('limites.index');
    }
}