<?php

namespace App\Http\Controllers;

use App\Models\Revista;
use App\Models\User;
use Illuminate\Http\Request;
use Uspdev\Replicado\Financeiro;
use Uspdev\Replicado\Pessoa;
use App\Util\ReplicadoTemp;

class RevistaController extends Controller
{
    /*
     * Exibe um index com todas as revistas;
     * Tabela com as colunas [Nome, Centro de Despesas, Responsável pelo Centro de Despesas, Ações]
     * Acesso exclusivo para usuários com a permissão 'admin-financeiro'
     */
    public function index()
    {   
        $this->authorize("admin-financeiro");

        $revistas = Revista::all();
        \UspTheme::activeUrl('revistas');
        
        return view('revistas.index', compact('revistas'));
    }

    /*
     * Exibe um formulario para cadastro de uma nova Revista
     * Traz do Replicado uma lista com todos centros de despesas
     * Traz do ReplicadoTemp uma lista com todos os servidores e docentes ativos de uma unidade específica
     * Acesso exclusivo para usuários com a permissão 'admin-financeiro'
     */
    public function create()
    {
        $this->authorize("admin-financeiro");
        
        $centros_despesas = Financeiro::ListarCentrosDespesas();
        $responsaveis_centro_despesa =  ReplicadoTemp::listarServidoresDocentes();
        
        \UspTheme::activeUrl('revistas/create');
        return view('revistas.create', compact('centros_despesas', 'responsaveis_centro_despesa'));
    }

    /**
     * Salva a nova Revista no banco de dados
     * Acesso exclusivo para usuários com a permissão 'admin-financeiro'
     */
    public function store(Request $request)
    {
        $this->authorize("admin-financeiro");

        $request->validate([
            'name' => 'required',
            'centro_despesa' => 'required',
            'responsavel_centro_despesa' => 'required',
        ]);

        $Revista = new Revista();
        $revista = [
            'editor' => $request->user()->id,
            "name" => $request->name,
            "centro_despesa" => $request->centro_despesa,
            'responsavel_centro_despesa' => $request->responsavel_centro_despesa,
        ];
        Revista::create($revista);
        session()->flash('success', 'Cadastrado com sucesso!');
        return redirect()->route('revistas.index');
    }

    /*
     * Exibe os dados de uma Revista
     * Os dados exibidos são: Centro de Despesas, Responsável pelo Centro de Despesas e uma tabela de Solicitantes
     * A tabela de Solicitantes tem as seguintes colunas: [Nome, E-mail, Número USP]
     * Traz informacoes do usuario que criou/editou essa revista por ultimo e a data da criacao/edicao
     * Acesso exclusivo para usuários com a permissão 'admin-financeiro'
     */
    public function show(Revista $revista)
    {
        $this->authorize("admin-financeiro");

        $editor = User::where('id', $revista->editor)->first();
        $updated_at = $revista->updated_at;
        $revista->solicitantes->load('user');

        return view('revistas.show', compact('revista', 'updated_at','editor' ) );
    }

    /**
     * Carrega o formulário de edição de uma Revista
     * Traz os dados de centros de despesas do Replicado
     * Traz os dados de servidores e docentes do ReplicadoTemp
     * Acesso exclusivo para usuários com a permissão 'admin-financeiro'
     */
    public function edit(Revista $revista)
    {
        $this->authorize("admin-financeiro");

        $centros_despesas = Financeiro::ListarCentrosDespesas();
        $responsaveis_centro_despesa =  ReplicadoTemp::listarServidoresDocentes();
        
        return view('revistas.edit', compact('revista', 'centros_despesas','responsaveis_centro_despesa'));
    }

    /*
     * Atualiza no banco de dados as informacoes editadas de uma Revista
     * Acesso exclusivo para usuários com a permissão 'admin-financeiro'
     */
    public function update(Request $request, Revista $revista)
    {
        $this->authorize("admin-financeiro");

        $request->validate([
            'name' => 'required',
            'centro_despesa' => 'required',
            'responsavel_centro_despesa' => 'required',
        ]);
        
        $revista_editada = [
            "editor" => $request->user()->id,
            "name" => $request->name,
            "centro_despesa" => $request->centro_despesa,
            "responsavel_centro_despesa" => $request->responsavel_centro_despesa,
        ];
        
        $revista->update($revista_editada);
        
        session()->flash('success', 'Atualizado com sucesso!');
        return redirect()->route('revistas.index');
    }

    /**
     * Remove uma revista do banco de dados.
     * Acesso exclusivo para usuários com a permissão 'admin-financeiro'
     */
    public function destroy(Revista $revista)
    {
        $this->authorize("admin-financeiro");
        
        $revista->delete();
        session()->flash('danger', 'Revista: ' . $revista->name . ' removida!');
        
        return redirect()->route('revistas.index');
    }
}