<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Empresa;
use App\Models\Contato;
use App\Models\EmpresaServico;
use App\Models\User;
use App\Models\EmpresaLicitacao;
use App\Models\Licitacao;
use App\Models\Servico;

class EmpresaController extends Controller
{
    //exibir o form para criar uma nova empresa
    public function create()
    {
        $this->authorize("admin-financeiro");
        
        $licitacoes = Licitacao::orderBy('ano', 'desc')->orderBy('descricao')->get();
        $servicos = Servico::orderBy('descricao', 'desc')->get();
        $unidades_medida = $stringArray = explode(",", config('revistas.unidadesMedida'));
        
        \UspTheme::activeUrl('empresas/create');
        
        return view('empresas.create', compact('licitacoes', 'servicos', 'unidades_medida'));
    }
    
    //armazenar a nova empresa
    public function store(Request $request)
    {
        $this->authorize("admin-financeiro");
        
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

        if ($request->has('servico_id')) {
            foreach ($request->servico_id as $servicoId) {
                EmpresaServico::create([
                    'empresa_id' => $empresa->id,
                    'servico_id' => $servicoId,
                ]);
            }
        }

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
        \UspTheme::activeUrl('empresas');

        // admin e financeiro podem ver todas as empresas
        if(auth()->user()->hasRole('admin') || auth()->user()->hasRole(roles: 'financeiro'))
        {
            $empresas = Empresa::orderBy('updated_at', 'desc')->get();
            return view('empresas.index', compact('empresas'));
        }

        // se o usuario for contato de uma empresa, pode ver só a empresa que ele está associado
        else if(Contato::where('user_id', auth()->id())->exists())
        {
            $contato = Contato::where('user_id', auth()->id())->first();
            $empresas = Empresa::where('id', $contato->empresa_id)->get();

            return view('empresas.index', compact('empresas'));
        }
        else
        {
            session()->flash('error', 'Usuário não está associado a nenhuma empresa!');
            
            return redirect()->route('home');
        }
    }
    
    // Exibir os detalhes de uma empresa
    public function show(Empresa $empresa)
    {
        // se for admin-financeiro, pode ver qualquer empresa.
        if(auth()->user()->hasAnyPermission(['revistas.revista']))
        {
            $updated_at = $empresa->updated_at;
            $editor = User::where('id', $empresa->editor)->first();
            $updated_at = $empresa->updated_at;
            $contatos = Contato::where('empresa_id', $empresa->id)->get();

            return view('empresas.show', compact('updated_at','empresa','contatos', 'editor'));
        }
        // se for contato, só pode ver a empresa que ele presta serviço
        else if(Contato::where('user_id', auth()->id())->exists())
        {
            $contato = Contato::where('user_id', auth()->id())->first();
            $empresas = Empresa::where('id', $contato->empresa_id)->pluck('id');
            if($empresas->contains($empresa->id))
            {
                $updated_at = $empresa->updated_at;
                $editor = User::where('id', $empresa->editor)->first();
                $updated_at = $empresa->updated_at;
                $contatos = Contato::where('empresa_id', $empresa->id)->get();

                return view('empresas.show', compact('updated_at','empresa','contatos', 'editor'));
            }
            else
            {
                session()->flash('error', 'Usuário não está associado a essa empresa!');
                
                return redirect()->route('home');
            }
        }
        else
        {
            session()->flash('error', 'Usuário não está associado a essa empresa!');
            
            return redirect()->route('home');
        }


    }
    
    // Exibir o formulário para editar uma empresa
    public function edit(Empresa $empresa)
    {
                $this->authorize("admin-financeiro");

        $licitacoes = Licitacao::orderBy('ano', 'desc')->orderBy('descricao')->get();
        $servicos = Servico::orderBy('descricao', 'desc')->get();
        $unidades_medida = $stringArray = explode(",", config('revistas.unidadesMedida'));
        // pega os serviços que estão associados a essa empresa;
        $servicos_associados = $empresa->servicos->pluck('id')->toArray();

        return view('empresas.edit', compact('empresa', 'licitacoes', 'servicos','unidades_medida', 'servicos_associados'));
    }
    
    // Atualizar os dados de uma empresa
    public function update(Request $request, Empresa $empresa)
    {
                $this->authorize("admin-financeiro");
  
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
                $this->authorize("admin-financeiro");

        $empresa->delete();
        
        session()->flash('danger', 'Empresa: ' . $empresa->cnpj . ' - ' . $empresa->razao_social . ' removida!');
        return redirect()->route('empresas.index');
    }
    
    public function empresasAjuda()
    {
        return view('empresas.ajuda');
    }
}