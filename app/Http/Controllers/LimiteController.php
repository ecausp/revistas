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

    /**
     * Define as regras de validação para criação e atualização de Limites.
     */
    private function getValidationRules(): array
    {
        return [
            'licitacao_id' => 'required|exists:licitacoes,id',
            'revista_id' => 'required|exists:revistas,id',
            'servico_id' => 'required|exists:servicos,id',
            'quantidade' => 'required|integer|min:1',
            'valor_unitario' => 'required|numeric|min:0',
        ];
    }
    /**
     * Busca os dados necessários para popular os formulários (create e edit).
     */
    private function getDependencies(): array
    {
        return [
            'licitacoes' => Licitacao::all(),
            'revistas' => Revista::all(),
            'servicos' => Servico::all(),
        ];
    }

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

        $data = $this->getDependencies();

        return view('limites.create', $data);
    }

    public function store(Request $request)
    {
        $this->authorize("admin-financeiro");
        
        $request->validate($this->getValidationRules());

        Limite::criarLimite($request);

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
        
        $data = $this->getDependencies();
        $data['limite'] = $limite;

        return view('limites.edit', $data);
    }

    public function update(Request $request, Limite $limite)
    {
        $this->authorize("admin-financeiro");

        $request->validate($this->getValidationRules());

        Limite::atualizarLimite($request, $limite);


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