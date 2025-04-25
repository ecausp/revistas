<?php

namespace App\Http\Controllers;

use App\Models\Servico;
use App\Models\User;
use Illuminate\Http\Request;

class ServicoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {   
        $this->authorize('servicos');
        \UspTheme::activeUrl('servicos');

        $servicos = Servico::all();
        
        return view('servicos.index', compact('servicos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('servicos');
        \UspTheme::activeUrl('servicos/create');

        $unidades_medida = $stringArray = explode(",", env('UNIDADES_MEDIDA'));

        return view('servicos.create', compact('unidades_medida'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('servicos');

        // Primeiro remove os pontos
        $request['quantidade'] = str_replace('.', '', $request->quantidade);       
        // Primeiro remove os pontos e depois troca a virgula por ponto
        $request['valor_unitario'] = str_replace(',', '.', str_replace('.', '', $request->valor_unitario));

        $validated = $request->validate([
            'descricao' => 'required',
            'quantidade' => 'required|numeric',
            'unidade_medida' => 'required',
            'valor_unitario' => 'required|numeric',
        ]);
    
        // Adiciona o editor manualmente
        $validated['editor'] = $request->user()->id;
    
        $servico = Servico::create($validated);

        if ($request->ajax()) {
            return response()->json([
                'id' => $servico->id,
                'descricao' => $servico->descricao,
                'unidade_medida' => $servico->unidade_medida,
            ]);
        }
        
        session()->flash('success', 'Cadastrado com sucesso!');
        return redirect()->route('servicos.index');
        
    }

    /**
     * Display the specified resource.
     */
    public function show(Servico $servico)
    {
        $this->authorize('servicos');

        $editor = User::where('id', $servico->editor)->first();
        $updated_at = $servico->updated_at;

        return view('servicos.show', compact('servico', 'updated_at', 'editor'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Servico $servico)
    {
        $this->authorize('servicos');

        $unidades_medida = $stringArray = explode(",", env('UNIDADES_MEDIDA'));
        return view('servicos.edit', compact('servico', 'unidades_medida'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Servico $servico)
    {
        $this->authorize('servicos');

        // Primeiro remove os pontos
        $request['quantidade'] = str_replace('.', '', $request->quantidade);       
        // Primeiro remove os pontos e depois troca a virgula por ponto
        $request['valor_unitario'] = str_replace(',', '.', str_replace('.', '', $request->valor_unitario));

        $request->validate([
            'descricao' => 'required',
            'quantidade' => 'required|numeric',
            'unidade_medida' => 'required',
            'valor_unitario' => 'required|numeric',
        ]);
        
        $servico_editado = [
            'editor' => $request->user()->id,
            "descicao" => $request->descricao,
            "quantidade" => $request->quantidade,
            "unidade_medida" => $request->unidade_medida,
            "valor_unitario" => $request->valor_unitario,
        ];
        
        $servico->update($servico_editado);
        session()->flash('success', 'Atualizado com sucesso!');
        return redirect()->route('servicos.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Servico $servico)
    {
        $this->authorize('servicos');

        $servico->delete();
        
        session()->flash('danger', 'Serviço: ' . $servico->descricao . ' removido!');
        return redirect()->route('servicos.index');
    }
}