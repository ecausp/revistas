<?php

namespace App\Http\Controllers;

use App\Models\Revista;
use App\Models\Solicitante;
use App\Models\User;
use App\Models\Saldo;
use Illuminate\Http\Request;
use Uspdev\Replicado\Pessoa;
use App\Util\ReplicadoTemp;

use Spatie\Permission\Models\Permission;
class RevistaController extends Controller
{
    /*
     * Exibe um index com todas as revistas;
     * Tabela com as colunas [Nome, Editor(es), Ações]
     * Acesso exclusivo para usuários com a permissão 'admin-financeiro'
     */
    public function index()
    {   
        \UspTheme::activeUrl('revistas');
        
        // pega todas as revistas visíveis para o usuário logado
        $revistas = auth()->user()->revistasVisiveis();

        // se o usuário for admin ou financeiro, ele pode ver todas as revistas, mesmo que nenhuma tenha sido criada
        if (auth()->user()->hasAnyRole(['admin', 'financeiro']) && $revistas->isEmpty()) {
            return view('revistas.index', compact('revistas'));
        }

        // se o usuário não pode ver nenhuma revista, retorna erro
        else if ($revistas->isEmpty()) {
                session()->flash('error', 'Usuário não está associado a nenhuma revista!');
            return redirect()->route('home');
        }

        // se ele pode ver alguma revista, carrega elas.
        return view('revistas.index', compact('revistas'));
    }
    /*
     * Exibe um formulario para cadastro de uma nova Revista
     * Traz do ReplicadoTemp uma lista com todos os servidores e docentes ativos de uma unidade específica
     * Acesso exclusivo para usuários com a permissão 'admin-financeiro'
     */
    public function create()
    {
        $this->authorize("admin-financeiro");
        
        $responsaveis_centro_despesa =  $this->listarServidoresDocentes();
        
        \UspTheme::activeUrl('revistas/create');
        return view('revistas.create', compact('responsaveis_centro_despesa'));
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
            'responsavel_centro_despesa' => 'required',
            'email_revista' => 'required',
        ]);

        $responsaveis_centro_despesa = Revista::ConveterResponsaveis($request->responsavel_centro_despesa);

        # cria a revista
        $revista = Revista::criarRevista($request, $responsaveis_centro_despesa);
        
        # cria os users caso não existam:
        foreach($request->responsavel_centro_despesa as $codpes)
        {
            $user = User::criarUsuario($codpes);
            # dá permissão de user (hierarquica) e revista (sistema)
            $user->atribuirPermissaoRevista();
            # cria o solicitante:
            Solicitante::criarSolicitante($user, $revista);

        }


        session()->flash('success', 'Cadastrado com sucesso!');
        return redirect()->route('revistas.index');
    }

    /*
     * Exibe os dados de uma Revista
     * Os dados exibidos são: editores, e-mail e uma tabela de solicitantes
     * A tabela de Solicitantes tem as seguintes colunas: [Nome, E-mail, Número USP]
     * Traz informacoes do usuario que criou/editou essa revista por ultimo e a data da criacao/edicao
     * Acesso exclusivo para usuários com a permissão 'admin-financeiro'
     */
    public function show(Revista $revista)
    {
        $editor = User::where('id', $revista->editor)->first();
        $updated_at = $revista->updated_at;
        $revista->solicitantes->load('user');
        $saldos = Saldo::where('revista_id', $revista->id)->get();
        
        // só deixa ver se a revista está dentro das visíveis do usuário
        if (!auth()->user()->revistasVisiveis()->contains('id', $revista->id)) {
            session()->flash('error', 'Usuário não autorizado a acessar esse recurso');
            return redirect()->route('home');
        }

        return view('revistas.show', compact('revista', 'updated_at','editor', 'saldos'));
    }

    /**
     * Carrega o formulário de edição de uma Revista
     * Traz os dados de servidores e docentes do ReplicadoTemp
     * Acesso exclusivo para usuários com a permissão 'admin-financeiro'
     */
    public function edit(Revista $revista)
    {
        $user = auth()->user();
        $solicitante = Solicitante::Where('user_id', $user->id)->first();
        if($user->hasAnyRole(['admin','financeiro']) || $solicitante != null && $solicitante->revista_id == $revista->id)
        {
            $responsaveis_centro_despesa =  $this->listarServidoresDocentes();
        
            return view('revistas.edit', compact('revista', 'responsaveis_centro_despesa'));
        }
        else
        {
            session()->flash('error', 'Usuário não autorizado a acessar esse recurso');
            return redirect()->route('home');
        }
    }

    /*
     * Atualiza no banco de dados as informacoes editadas de uma Revista
     * Acesso exclusivo para usuários com a permissão 'admin-financeiro'
     */

    public function update(Request $request, Revista $revista)
    {

        $request->validate([
            'name' => 'required',
            'responsavel_centro_despesa' => 'required',
            'email_revista' => 'required',
        ]);


        // array com os responsáveis vindos do formulário
        $responsaveisArray = $request->responsavel_centro_despesa;

        // converte para string e atualiza a revista
        $responsaveisString = Revista::conveterResponsaveis($responsaveisArray);

        // Atualiza a revista e trata troca de responsável
        $revista->atualizarRevista($request, $responsaveisString);

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


    protected function listarServidoresDocentes()
    {
        return ReplicadoTemp::listarServidoresDocentes();
    }
}
