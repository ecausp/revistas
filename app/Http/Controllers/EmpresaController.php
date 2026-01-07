<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Empresa;
use App\Models\Contato;
use App\Models\User;
use App\Models\EmpresaLicitacao;
use App\Models\Licitacao;

class EmpresaController extends Controller
{
    //exibir o form para criar uma nova empresa
    public function create()
    {
        $this->authorize("admin-financeiro");
        \UspTheme::activeUrl('empresas/create');
        
        return view('empresas.create');
    }
    
    //armazenar a nova empresa
    public function store(Request $request)
    {
        $this->authorize("admin-financeiro");

        // Validação dos campos obrigatórios
        $request->validate([
            'cnpj' => 'required',
            'razao_social' => 'required',
        ]);

        // Cria a empresa
        Empresa::create([
            'editor' => $request->user()->id,
            'cnpj' => $request->cnpj,
            'razao_social' => $request->razao_social,
        ]);

        session()->flash('success', 'Empresa cadastrada com sucesso!');
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

        return view('empresas.edit', compact('empresa'));
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

        // Atualiza apenas os campos principais da empresa
        $empresa->update([
            'editor' => $request->user()->id,
            'cnpj' => $request->cnpj,
            'razao_social' => $request->razao_social,
        ]);

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