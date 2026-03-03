<?php

namespace App\Http\Controllers;

use App\Models\Servico;
use App\Models\User;
use App\Models\Contato;
use App\Models\Empresa;
use App\Models\EmpresaLicitacaoServico;
use Illuminate\Http\Request;

class ServicoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {   
        \UspTheme::activeUrl('servicos');
        // admin e financeiro podem visualizar todos os servicos
        if(auth()->user()->hasRole('admin') || auth()->user()->hasRole(roles: 'financeiro'))
        {
            $servicos = Servico::all();
            return view('servicos.index', compact('servicos'));
        }
        // se o usuario for contato de uma empresa, só pode ver os serviços prestados pela sua empresa
        else if(Contato::where('user_id', auth()->id())->exists())
        {
            $contato = Contato::where('user_id', auth()->id())->first();
            $empresa = Empresa::where('id', $contato->empresa_id)->first();
            $servicos = $empresa->servicos->pluck('servico');
            
            return view('servicos.index', compact('servicos'));
        }
        else
        {
            session()->flash('error', 'Usuário não está associado a nenhuma empresa!');
            
            return redirect()->route('home');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize("admin-financeiro");
        \UspTheme::activeUrl('servicos/create');

        $unidades_medida = $stringArray = explode(",", config('revistas.unidadesMedida'));

        return view('servicos.create', compact('unidades_medida'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
                $this->authorize("admin-financeiro");

        // Primeiro remove os pontos e depois troca a virgula por ponto
        $request['valor_unitario'] = str_replace(',', '.', str_replace('.', '', $request->valor_unitario));

        $validated = $request->validate([
            'descricao' => 'required',
            'unidade_medida' => 'required',
        ]);
    
        // Adiciona o editor manualmente
        $validated['editor'] = $request->user()->id;
    
        $servico = Servico::create($validated);

        if ($request->ajax()) {
            return response()->json([
                'id' => $servico->id,
                'descricao' => $servico->descricao,
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
        // se o usuario logado for contato de uma empresa, só pode abrir os serviços que ele presta
        if(Contato::where('user_id', auth()->id())->first())
        {
            $contato = Contato::where('user_id', auth()->id())->first();
            $empresa_do_contato = Empresa::where('id', $contato->empresa_id)->first();
            $relacaoEmpresaServico = EmpresaLicitacaoServico::where('servico_id', $servico->id)->first();
            $empresas_prestantes = $relacaoEmpresaServico->empresa_id;

            if($empresas_prestantes == $empresa_do_contato->id)
            {
                $editor = User::where('id', $servico->editor)->first();
                $updated_at = $servico->updated_at;

                return view('servicos.show', compact('servico', 'updated_at', 'editor')); 
            }
            else
            {
                session()->flash('error', 'Usuário não autorizado a acessar esse recurso');
        
                return redirect()->route('home');

            }
        }
        // se for admin ou financeiro, pode ver todos
        else if(auth()->user()->hasRole('admin') || auth()->user()->hasRole(roles: 'financeiro'))
        {
            $editor = User::where('id', $servico->editor)->first();
            $updated_at = $servico->updated_at;

            return view('servicos.show', compact('servico', 'updated_at', 'editor'));
        }

        else
        {
            session()->flash('error', 'Usuário não autorizado a acessar esse recurso');
    
            return redirect()->route('home');

        }

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Servico $servico)
    {
                $this->authorize("admin-financeiro");

        $unidades_medida = $stringArray = explode(",", config('revistas.unidadesMedida'));
        return view('servicos.edit', compact('servico', 'unidades_medida'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Servico $servico)
    {
        $this->authorize("admin-financeiro");


        $request->validate([
            'descricao' => 'required',
            'unidade_medida' => 'required',
        ]);
        
        $servico_editado = [
            'editor' => $request->user()->id,
            "descricao" => $request->descricao,
            "unidade_medida" => $request->unidade_medida,
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
        $this->authorize("admin-financeiro");

        $servico->delete();
        
        session()->flash('danger', 'Serviço: ' . $servico->descricao . ' removido!');
        return redirect()->route('servicos.index');
    }


    // para preencher o valor no form do limite
    public function valorUnitario($id)
{
    $servico = Servico::findOrFail($id);

    return response()->json([
        'valor_unitario' => $servico->valor_unitario,
    ]);
}

}