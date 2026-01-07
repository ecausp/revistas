<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Empresa;
use App\Models\Contato;
use App\Models\User;
use App\Models\EmpresaLicitacaoServico;
use App\Models\Servico;

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

    public function relacionarLicitacoesServicos(Empresa $empresa)
    {
        $this->authorize("admin-financeiro");

        // 1) Pega todas as licitações associadas à empresa
        $licitacoes_associadas = $empresa->licitacoes()->orderBy('ano', 'desc')->orderBy('descricao')->get();

        // 2) Pega todos os serviços cadastrados no sistema
        $todos_servicos = Servico::orderBy('descricao')->get();

        // 3) Pega todos os serviços já associados à empresa e a cada licitação
        $servicos_associados = EmpresaLicitacaoServico::where('empresa_id', $empresa->id)
            ->whereIn('licitacao_id', $licitacoes_associadas->pluck('id')) // filtra apenas licitações da empresa
            ->with('servico') // carrega os dados do serviço relacionado
            ->get();

        // 4) Monta um array organizado por licitação para a view
        $servicos_para_view = [];
        foreach ($servicos_associados as $els) {
            $servicos_para_view[$els->licitacao_id][] = [
                'id' => $els->servico->id,
                'descricao' => $els->servico->descricao,
                'valor_unitario' => $els->valor_unitario,
                'prazo' => $els->prazo,
            ];
        }

        return view('empresas.relacionar-licitacoes-servicos', compact(
            'empresa','licitacoes_associadas','todos_servicos', 'servicos_para_view'));
    }

    public function salvarRelacionamento(Request $request, Empresa $empresa)
    {
        $this->authorize('admin-financeiro');

        // Converte valores monetários do formato brasileiro
        $servicos = $request->input('servicos', []);

        foreach ($servicos as $i => $servico) {
            if (isset($servico['valor_unitario'])) {
                $servicos[$i]['valor_unitario'] = str_replace(
                    ',',
                    '.',
                    str_replace('.', '', $servico['valor_unitario'])
                );
            }
        }

        // Reinsere os dados tratados no request
        $request->merge([
            'servicos' => $servicos
        ]);

        $request->validate([
            'licitacao_id' => 'required|exists:licitacoes,id',
            'servicos' => 'required|array|min:1',
            'servicos.*.id' => 'required|exists:servicos,id',
            'servicos.*.valor_unitario' => 'required|numeric|min:0',
            'servicos.*.prazo' => 'required|integer|min:0',
        ]);

        $licitacaoId = $request->licitacao_id;

        EmpresaLicitacaoServico::where('empresa_id', $empresa->id)
            ->where('licitacao_id', $licitacaoId)
            ->delete();

        foreach ($request->servicos as $servico) {
            EmpresaLicitacaoServico::create([
                'empresa_id' => $empresa->id,
                'licitacao_id' => $licitacaoId,
                'servico_id' => $servico['id'],
                'valor_unitario' => $servico['valor_unitario'],
                'prazo' => $servico['prazo'],
            ]);
        }
        
        session()->flash('success', 'Relacionamento entre empresa, licitação e serviços salvo com sucesso.');

        return redirect()->route('empresas.show', $empresa);
    }


}