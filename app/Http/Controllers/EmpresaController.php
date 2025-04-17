<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\Contato;
use App\Models\EmpresaServico;
use App\Models\User;
use App\Models\EmpresaLicitacao;
use App\Models\Licitacao;
use App\Models\Servico;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EmpresaController extends Controller
{
    //exibir o form para criar uma nova empresa
    public function create()
    {
        $this->authorize('admin');
        
        $licitacoes = Licitacao::orderBy('ano', 'desc')->orderBy('descricao')->get();
        \UspTheme::activeUrl('empresas/create');
        return view('empresas.create', compact('licitacoes'));
    }
    
    //armazenar a nova empresa
    public function store(Request $request)
    {
        $this->authorize('admin');
        
        $request->validate([
            'cnpj' => 'required',
            'razao_social' => 'required',
            'licitacao_id' => 'required',
        ]);

        $empresa = new Empresa();
        $empresa->editor = $request->user()->id;
        $empresa->cnpj = $request->cnpj;
        $empresa->razao_social = $request->razao_social;
        $empresa->save();

        $relacao = new EmpresaLicitacao();
        $relacao->empresa_id = $empresa->id;
        $relacao->licitacao_id = $request->licitacao_id;
        $relacao->valor = 1000;
        $relacao->save();
        session()->flash('success', 'Cadastrado com sucesso!');
        return redirect()->route('empresas.index');
    }

    // listar todas as empresas
    public function index()
    {
        $this->authorize('admin');
        $empresas = Empresa::orderBy('updated_at', 'desc')->get();
        \UspTheme::activeUrl('empresas');
        return view('empresas.index', compact('empresas'));
    }
    
    // Exibir os detalhes de uma empresa
    public function show(Empresa $empresa)
    {

        $this->authorize('admin');
        $editor = User::where('id', $empresa->editor)->first();
        $updated_at = $empresa->updated_at;
        $contatos = Contato::where('empresa_id', $empresa->id)->get();
      
        $users = [];
        $i = 0;
        foreach ($contatos as $contato) {
            $users[$i] = User::where('id', $contato->user_id)->get()->all();
            Log::debug('usuario:', $users[$i]);
            $i++;
        };
        
        return view('empresas.show', compact('empresa','users'));

    }
    
    // Exibir o formulário para editar uma empresa
    public function edit(Empresa $empresa)
    {
        $this->authorize('admin');
        $licitacoes = Licitacao::orderBy('ano', 'desc')->orderBy('descricao')->get();
        $servicos = Servico::orderBy('descricao', 'desc')->get();

        $unidades_medida = $stringArray = explode(",", env('UNIDADES_MEDIDA'));

        // pega os serviços que estão associados a essa empresa;
        $servicos_associados = $empresa->servicos->pluck('id')->toArray();
        return view('empresas.edit', compact('empresa', 'licitacoes', 'servicos','unidades_medida', 'servicos_associados'));
    }
    
    // Atualizar os dados de uma empresa
    public function update(Request $request, Empresa $empresa)
    {
        $this->authorize('admin');
  
        // Validação dos campos
        $request->validate([
            'cnpj' => 'required',
            'razao_social' => 'required',
        ]);
        
        // Dados para atualização da empresa
        $empresa_editada = [
            'editor' => $request->user()->id,
            "cnpj" => $request->cnpj,
            "razao_social" => $request->razao_social,
        ];
        
        // Atualiza a empresa
        $empresa->update($empresa_editada);
        
        // Remove relações antigas entre empresa e serviços
        EmpresaServico::where('empresa_id', $empresa->id)->delete();
                
        if ($request->has('servico_id')) {
            foreach ($request->servico_id as $servicoId) {
                EmpresaServico::create([
                    'empresa_id' => $empresa->id,
                    'servico_id' => $servicoId,
                ]);
            }
        }

        
        session()->flash('success', 'Atualizado com sucesso!');
        return redirect()->route('empresas.index');
    }
    
    
    
    public function destroy(Empresa $empresa)
    {
        $this->authorize('admin');
        $empresa->delete();
        session()->flash('danger', 'Empresa: ' . $empresa->cnpj . ' - ' . $empresa->razao_social . ' removida!');
        return redirect()->route('empresas.index');
        }
    
}