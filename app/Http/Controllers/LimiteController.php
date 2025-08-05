<?php

namespace App\Http\Controllers;

use App\Models\Licitacao;
use App\Models\Limite;
use App\Models\Revista;
use App\Models\Servico;
use App\Models\User;
use Illuminate\Http\Request;

class LimiteController extends Controller
{
    //lista os Limites
    public function index()
    {
        $this->authorize("admin-financeiro");
        \UspTheme::activeUrl('limites');
        
        $limites = Limite::all();
        
        return view('limites.index', compact('limites'));
    }

    public function create()
    {
        $this->authorize("admin-financeiro");
        \UspTheme::activeUrl('limites/create');

        $licitacoes = Licitacao::all();
        $revistas = Revista::all();
        $servicos = Servico::all();

        return view('limites.create', compact('licitacoes', 'revistas', 'servicos'));
    }

    public function store(Request $request)
    {
        $this->authorize("admin-financeiro");

        $request->validate([
            'licitacao_id' => 'required|exists:licitacoes,id',
            'revista_id' => 'required|exists:revistas,id',
            'servico_id' => 'required|exists:servicos,id',
            'quantidade' => 'required|integer|min:1',
            'valor_unitario' => 'required|numeric|min:0',
        ]);

        $limite = new Limite();

        $limite = [
            'licitacao_id' => $request->licitacao_id,
            'revista_id' => $request->revista_id,
            'servico_id' => $request->servico_id,
            'quantidade' => $request->quantidade,
            'valor_unitario' => $request->valor_unitario,
            'editor' => $request->user()->id,
        ];

        

        $novo_limite = Limite::create($limite);
        session()->flash('success', 'Cadastrado com sucesso!');
        return redirect()->route('limites.index');
    }

    public function show(Limite $limite)
    {
        $this->authorize("admin-financeiro");
        $editor = User::where('id', $limite->editor)->first();
        $updated_at = $limite->updated_at;

        return view('limites.show', compact('limite','editor', 'updated_at'));
    }

    public function edit(Limite $limite)
    {
        $this->authorize("admin-financeiro");
        
        $licitacoes = Licitacao::all();
        $revistas = Revista::all();
        $servicos = Servico::all();

        return view('limites.edit', compact('limite','licitacoes', 'revistas', 'servicos'));
    }

    public function update(Request $request, Limite $limite)
    {
        $this->authorize("admin-financeiro");

        $request->validate([
            'licitacao_id' => 'required|exists:licitacoes,id',
            'revista_id' => 'required|exists:revistas,id',
            'servico_id' => 'required|exists:servicos,id',
            'quantidade' => 'required|integer|min:1',
            'valor_unitario' => 'required|numeric|min:0',
        ]);

        $limite_editado = [
            'licitacao_id' => $request->licitacao_id,
            'revista_id' => $request->revista_id,
            'servico_id' => $request->servico_id,
            'quantidade' => $request->quantidade,
            'valor_unitario' => $request->valor_unitario,
            'editor' => $request->user()->id,
        ];

        $limite->update($limite_editado);

        session()->flash('success', 'Atualizado com sucesso!');
        return redirect()->route('limites.index');
    }

    public function destroy(Limite $limite)
    {
        $this->authorize("admin-financeiro");

        $limite->delete();

        session()->flash('danger', 'Limite: ' . $limite->id . ' removida!');
        return redirect()->route('limites.index');
    }
}