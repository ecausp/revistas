<?php

namespace App\Http\Controllers;

use App\Models\Licitacao;
use App\Models\Empresa;
use App\Models\User;
use App\Models\Solicitante;
use App\Models\Saldo;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class LicitacaoController extends Controller
{
    //exibir o form para criar uma nova licitacao
    public function create()
    {
        $this->authorize("admin-financeiro");
        \UspTheme::activeUrl('licitacoes/create');

        return view('licitacoes.create');
    }

    //armazenar a nova licitacao
    public function store(Request $request)
    {
        $this->authorize("admin-financeiro");
        
        // Primeiro remove os pontos e depois troca a virgula por ponto
        $request['valor_total'] = str_replace(',', '.', str_replace('.', '', $request->valor_total));
        
        $request->validate([
            'descricao' => 'required',
            'ano' => 'required|digits:4',
            'valor_total' => 'required|numeric',
        ]);
 
        Licitacao::create([
            'editor' => $request->user()->id,
            'descricao' => $request->descricao,
            'ano' => $request->ano,
            'valor_total' => $request->valor_total,
        ]);

        session()->flash('success', 'Cadastrado com sucesso!');
        return redirect()->route('licitacoes.index');
    }

    // listar todas as licitacoes
    public function index()
    {
        \UspTheme::activeUrl('licitacoes');

        $licitacoes = Licitacao::with('empresas')->orderBy('ano', 'desc')->orderBy('descricao')->get();
        $empresas = Empresa::get();
        return view('licitacoes.index', compact('licitacoes', 'empresas'));
    }

    // Exibir os detalhes de uma licitação
    public function show(Licitacao $licitacao)
    {
        $saldos = NULL;
        $editor = User::find($licitacao->editor);

        $updated_at = $licitacao->updated_at;
        if(auth()->user()->hasRole(roles: 'financeiro'))
        {
            $saldos = Saldo::all();
        }
        // verificar se é user revista e qual revista ele faz parte pra ver só a sua revista;
        else if(Solicitante::where('user_id', auth()->id())->exists())
        {

            $solicitante = Solicitante::where('user_id', auth()->id())->first();
            $saldos = Saldo::where('revista_id', $solicitante->revista_id)->get();
        }
        return view('licitacoes.show', compact("licitacao", 'updated_at', "editor", 'saldos'));
    }

    // Exibir o formulário para editar uma licitação
    public function edit(Licitacao $licitacao)
    {
        $this->authorize("admin-financeiro");

        return view('licitacoes.edit', compact('licitacao'));
    }

    // Atualizar os dados de uma licitação
    public function update(Request $request, Licitacao $licitacao)
    {
        $this->authorize("admin-financeiro");

        // Primeiro remove os pontos e depois troca a virgula por ponto
        $request['valor_total'] = str_replace(',', '.', str_replace('.', '', $request->valor_total));
        
        $request->validate([
            'descricao' => 'required',
            'ano' => 'required|digits:4',
            'valor_total' => 'required|numeric',
        ]);

        $licitacao->update([
            'editor' => $request->user()->id,
            'descricao' => $request->descricao,
            'ano' => $request->ano,
            'valor_total' => $request->valor_total,
        ]);


        session()->flash('success', 'Atualizado com sucesso!');
        return redirect()->route('licitacoes.index');
    }

    public function destroy(Licitacao $licitacao)
    {
        $this->authorize("admin-financeiro");

        $licitacao->delete();
        
        session()->flash('danger', 'Licitação: ' . $licitacao->descricao . ' - ' . $licitacao->ano . ' removida!');
        return redirect()->route('licitacoes.index');
    }

    public function addEmpresas(Request $request, Licitacao $licitacao)
    {
        $this->authorize('admin-financeiro');

        $empresas = collect($request->input('empresas', []))
            ->filter(fn ($dados) => !empty($dados['selecionada']))
            ->all();

        $validator = Validator::make([
            'empresas' => $empresas,
            'empresa_vigente' => $request->input('empresa_vigente'),
        ], [
            'empresas' => 'required|array|min:1',
            'empresas.*.classificacao' => 'required|integer|min:1|distinct',
            'empresas.*.motivo_substituicao' => 'nullable|string|max:2000',
            'empresa_vigente' => 'required|integer|exists:empresas,id',
        ]);

        $validated = $validator->validate();
        $empresaVigenteId = (int) $validated['empresa_vigente'];
        $empresasIds = array_map('intval', array_keys($validated['empresas']));

        if (!in_array($empresaVigenteId, $empresasIds, true)) {
            throw ValidationException::withMessages([
                'empresa_vigente' => 'A empresa vigente deve estar entre as empresas classificadas.',
            ]);
        }

        $classificacoes = collect($validated['empresas'])
            ->pluck('classificacao')
            ->map(fn ($classificacao) => (int) $classificacao)
            ->sort()
            ->values()
            ->all();

        if ($classificacoes !== range(1, count($classificacoes))) {
            throw ValidationException::withMessages([
                'empresas' => 'As classificações devem ser sequenciais, começando em 1.',
            ]);
        }

        $dadosVigente = $validated['empresas'][$empresaVigenteId];
        if ((int) $dadosVigente['classificacao'] > 1 && blank($dadosVigente['motivo_substituicao'] ?? null)) {
            throw ValidationException::withMessages([
                "empresas.{$empresaVigenteId}.motivo_substituicao" =>
                    'Informe o motivo da substituição da empresa vencedora.',
            ]);
        }

        $empresasJaAssociadas = $licitacao->empresas()->pluck('empresas.id')->map(fn ($id) => (int) $id);
        if ($empresasJaAssociadas->diff($empresasIds)->isNotEmpty()) {
            throw ValidationException::withMessages([
                'empresas' => 'Empresas já associadas à licitação devem permanecer classificadas.',
            ]);
        }

        $dadosSincronizacao = collect($validated['empresas'])->mapWithKeys(
            fn ($dados, $empresaId) => [
                $empresaId => [
                    'classificacao' => (int) $dados['classificacao'],
                    'vigente' => (int) $empresaId === $empresaVigenteId,
                    'motivo_substituicao' => $dados['motivo_substituicao'] ?? null,
                ],
            ]
        )->all();

        DB::transaction(function () use ($licitacao, $dadosSincronizacao) {
            Licitacao::whereKey($licitacao->id)->lockForUpdate()->first();

            DB::table('empresa_licitacao')
                ->where('licitacao_id', $licitacao->id)
                ->update([
                    'classificacao' => DB::raw('classificacao + 1000000'),
                    'vigente' => false,
                ]);

            $licitacao->empresas()->syncWithoutDetaching($dadosSincronizacao);
        });

        session()->flash('success', 'Atualizado com sucesso!');
        return redirect()->route('licitacoes.index');
    }

}
