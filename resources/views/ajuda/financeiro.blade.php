<!-- resources/views/empresas/ajuda.blade.php -->
@extends('layouts.app')

@section('content')

<div class="card">
    <div class="card-header h5">
        Ajudas para o setor Financeiro
    </div>

    <div class="card-body">

		<div class="accordion" id="accordionAjuda">
		  
		  <div class="card">
			<div class="card-header" id="heading1">
			  <h2 class="mb-0">
				<button class="btn btn-link btn-block text-left" type="button" data-toggle="collapse" data-target="#collapse1" aria-expanded="true" aria-controls="collapse1">
				  1. Cadastros Básicos
				</button>
			  </h2>
			</div>
			<div id="collapse1" class="collapse" aria-labelledby="heading1" data-parent="#accordionAjuda">
			  <div class="card-body">
          É importante notar que alguns desses elementos podem ser reaproveitados em outros anos para outras licitações (como por exemplo serviços, empresas e revistas). <br>
                <ol>
                    <li>
                      No menu superior <span class="badge badge-light p-2"><i class="fas fa-tools" aria-hidden="true"> </i> Financeiro</span>, dentro do submenu 
                      <span class="badge badge-light p-2"><i class="fas fa-shopping-cart" aria-hidden="true"> </i> Licitações</span>
                      clique no botão <span class="badge badge-light p-2"><i class="fas fa-plus-circle" aria-hidden="true"> </i> Cadastrar</span> para cadastrar uma nova Licitação.
                    </li>
                    <li>
                      No menu superior <span class="badge badge-light p-2"><i class="fas fa-tools" aria-hidden="true"> </i> Financeiro</span>, dentro do submenu 
                      <span class="badge badge-light p-2"><i class="fas fa-building" aria-hidden="true"> </i> Empresas</span>
                      clique no botão <span class="badge badge-light p-2"><i class="fas fa-plus-circle" aria-hidden="true"> </i> Cadastrar</span> para cadastrar uma nova Empresa.
                    </li>
                    <li>
                      No menu superior <span class="badge badge-light p-2"><i class="fas fa-tools" aria-hidden="true"> </i> Financeiro</span>, dentro do submenu 
                      <span class="badge badge-light p-2"><i class="fas fa-tasks" aria-hidden="true"> </i> Serviços</span>
                      clique no botão <span class="badge badge-light p-2"><i class="fas fa-plus-circle" aria-hidden="true"> </i> Cadastrar</span> para cadastrar um novo Serviço.
                    </li>
                      <li>
                      No menu superior <span class="badge badge-light p-2"><i class="fas fa-tools" aria-hidden="true"> </i> Financeiro</span>, dentro do submenu 
                      <span class="badge badge-light p-2"><i class="fas fa-book" aria-hidden="true"> </i> Revistas</span>
                      clique no botão <span class="badge badge-light p-2"><i class="fas fa-plus-circle" aria-hidden="true"> </i> Cadastrar</span> para cadastrar uma nova Revista.
                    </li>
                </ol>
            
			  </div>
			</div>
		  </div>
		  
		  <div class="card">
			<div class="card-header" id="heading2">
			  <h2 class="mb-0">
				<button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#collapse2" aria-expanded="false" aria-controls="collapse2">
				  2. Indicar que a Empresa venceu uma Licitação
				</button>
			  </h2>
			</div>
			<div id="collapse2" class="collapse" aria-labelledby="heading2" data-parent="#accordionAjuda">
			  <div class="card-body">
                <ol>
                    <li>
                      No menu superior <span class="badge badge-light p-2"><i class="fas fa-tools" aria-hidden="true"> </i> Financeiro</span>, dentro do submenu 
                      <span class="badge badge-light p-2"><i class="fas fa-shopping-cart" aria-hidden="true"> </i> Licitações</span>
                      clique no botão <span class="badge badge-light p-2"><i class="fas fa-list-ul" aria-hidden="true"> </i> Listar</span> para listar todas as licitações.
                    </li>
                    <li>
                      Na tabela de licitações, sob a coluna <span class="badge badge-light p-2">Ações</span> clique no botão 
                      <span class="badge badge-primary p-2"><i class="fas fa-building" aria-hidden="true"> </i></span> para abrir o menu de associação entre Licitação e Empresa.
                    </li>
                    <li>
                      Esse menu contém um campo que traz todas as empresas que estão cadastradas no sistema, bata selecionar a empresa desejada e clicar em 
                      <span class="badge badge-primary p-2"><i class="fas fa-save" aria-hidden="true"></i>Salvar</span> para salvar.
                    </li>
                    <li>
                      Caso a empresa desejada não seja encontrada, basta clicar em 
                      <span class="badge badge-primary p-2"><i class="fas fa-plus" aria-hidden="true"></i>Cadastrar nova empresa</span> para ser redirecionado para a página de cadastro de uma nova empresa.
                    </li>
                    <li>Cao seja necessário cadastrar uma nova empresa, basta repetir esse passo-a-passo para associá-la com a licitação.</li>
                </ol>
			  </div>
			</div>
		  </div>
		  
      <div class="card">
			<div class="card-header" id="heading3">
			  <h2 class="mb-0">
				<button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#collapse3" aria-expanded="false" aria-controls="collapse3">
				  3. Cadastrar Recursos Financeiros das Revistas
				</button>
			  </h2>
			</div>
			<div id="collapse3" class="collapse" aria-labelledby="heading3" data-parent="#accordionAjuda">
			  <div class="card-body">
                <ol>
                    <li>
                      No menu superior <span class="badge badge-light p-2"><i class="fas fa-tools" aria-hidden="true"> </i> Financeiro</span>, dentro do submenu 
                      <span class="badge badge-light p-2"><i class="fas fa-shopping-cart" aria-hidden="true"> </i> Licitações</span>
                      clique no botão <span class="badge badge-light p-2"><i class="fas fa-list-ul" aria-hidden="true"> </i> Listar</span> para listar todas as licitações.
                    </li>
                    <li>
                      Na tabela de licitações, sob a coluna <span class="badge badge-light p-2">Ações</span> clique no botão 
                      <span class="badge badge-primary p-2"><i class="fas fa-edit" aria-hidden="true"> </i></span> para abrir a página de Edição da Licitação.
                    </li>
                    <li>
                      Na página de Edição da Licitação, clique no botão <span class="badge badge-success p-2"><i class="fas fa-money-bill" aria-hidden="true"> </i> Recursos Financeiros</span> para abrir o menu de Recursos Financeiros para abrir a página de cadastro de Recursos Financeiros.
                    </li>
                    <li>
                      Na página de cadastro de Recursos Financeiros, haverá um menu onde é possível selecionar todas as revistas para as quais se deseja cadastrar os valores dos Recursos Financeiros.
                    </li>
                    <li>
                      Haverá abaixo uma tabela com uma linha para cada revista selecionada e, em cada uma dessas linhas da tabela, um campo para cadastrar o Recurso Recebido e o Recurso Reservado para o Estágio.
                    </li>
                    <li>
                      Após inserir essas informações, basta clicar no botão <span class="badge badge-success p-2"><i class="fas fa-check" aria-hidden="true"> </i> </span> para salvar os Recursos Financeiros da Revista correspondente à linha da tabela.
                    </li>
                </ol>
			  </div>
			</div>
		  </div>
		  
       <div class="card">
			<div class="card-header" id="heading4">
			  <h2 class="mb-0">
				<button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#collapse4" aria-expanded="false" aria-controls="collapse4">
				  4. Cadastrar Contato da Empresa
				</button>
			  </h2>
			</div>
			<div id="collapse4" class="collapse" aria-labelledby="heading4" data-parent="#accordionAjuda">
			  <div class="card-body">
                <ol>
                  <li>
                     No menu superior <span class="badge badge-light p-2"><i class="fas fa-tools" aria-hidden="true"> </i> Financeiro</span>, dentro do submenu 
                      <span class="badge badge-light p-2"><i class="fas fa-building" aria-hidden="true"> </i> Empresas</span>
                      clique no botão <span class="badge badge-light p-2"><i class="fas fa-list-ul" aria-hidden="true"> </i> Listar</span> para listar todas as Empresas.
                  </li>
                  <li>
                    Na tabela de Empresas, sob a coluna <span class="badge badge-light p-2"> Ações</span>, clique no botão 
                    <span class="badge badge-primary p-2"><i class="fas fa-edit" aria-hidden="true"> </i></span> para abrir a página de Edição da Empresa.
                  </li>
                  <li>
                    Na página de Edição da Empresa, clique no botão <span class="badge badge-success p-2"><i class="fas fa-user" aria-hidden="true"> </i> Contato(s)</span> 
                    para abrir o menu de Contato(s) para abrir a página de Cadastro de Contato da Empresa
                  </li>
                  <li>
                    Preencha no formulário as informações do Contato e, quando tiver terminado, basta clicar em 
                     <span class="badge badge-primary p-2"><i class="fas fa-save" aria-hidden="true"> </i> Cadastrar</span>  para finalizar o cadastro.
                  </li>
                  <li>
                    O Contato receberá no e-mail indicado no formulário uma mensagem com instruções para que crie sua senha de acesso ao sistema.
                  </li>
                </ol>
			  </div>
			</div>
		  </div>

       <div class="card">
			<div class="card-header" id="heading5">
			  <h2 class="mb-0">
				<button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#collapse5" aria-expanded="false" aria-controls="collapse5">
				  5. Definir Valor Unitário e Prazo de um Serviço para uma Licitação
				</button>
			  </h2>
			</div>
			<div id="collapse5" class="collapse" aria-labelledby="heading5" data-parent="#accordionAjuda">
			  <div class="card-body">
                <ol>
                  <li>
                    No menu superior <span class="badge badge-light p-2"><i class="fas fa-tools" aria-hidden="true"> </i> Financeiro</span>, dentro do submenu 
                      <span class="badge badge-light p-2"><i class="fas fa-building" aria-hidden="true"> </i> Empresas</span>
                      clique no botão <span class="badge badge-light p-2"><i class="fas fa-list-ul" aria-hidden="true"> </i> Listar</span> para listar todas as Empresas.
                  </li>
                  <li>
                     Na tabela de Empresas, sob a coluna <span class="badge badge-light p-2"> Ações</span>, clique no botão 
                    <span class="badge badge-primary p-2"><i class="fas fa-plus" aria-hidden="true"> </i></span> para abrir a página de Relacionar Licitações e Serviços para a Empresa.
                  </li>
                  <li>
                    Nessa página, selecione a Licitação e os Serviços que deseja relacionar a essa Empresa.
                  </li>
                  <li>
                    Preencha o Valor Unitário e o Prazo de cada Serviço que será prestado por essa Empresa nessa Licitação.
                  </li>
                  <li>
                    Clique em  <span class="badge badge-primary p-2"><i class="fas fa-save" aria-hidden="true"> </i> Salvar</span> para salvar a relação.
                  </li>
                </ol>
			  </div>
			</div>
		  </div>

      <div class="card">
			<div class="card-header" id="heading6">
			  <h2 class="mb-0">
				<button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#collapse6" aria-expanded="false" aria-controls="collapse6">
				  6. Momento dos Cadastros
				</button>
			  </h2>
			</div>
			<div id="collapse6" class="collapse" aria-labelledby="heading6" data-parent="#accordionAjuda">
			  <div class="card-body">
                <ul>
                  <li>
                    A licitação pode ser cadastrada no sistema assim que ela for feita, já que não depende de outros dados.
                  </li>
                  <li>
                    A empresa pode ser cadastrada assim que vencer a licitação. É importante notar que, caso essa empresa já esteja cadastrada no sistema, basta relacioná-la com a nova licitação para evitar redundância nos dados.
                  </li>
                  <li>
                    O mesmo vale para o cadastro do contato da Empresa, só é preciso fazer um novo cadastro caso haja um novo contato, de resto, basta usar os dados que já constam no sistema.
                  </li>
                  <li>
                    Os serviços seguem a mesma lógica. Como alguns serviços podem ser efetuados em diversas licitações (como tradução inglês-português, por exemplo), basta atualizar a relação com a nova licitação e a nova empresa (caso exista) que define seu valor unitário e prazo.
                  </li>
                  <li>
                    As revistas também tem seu cadastro persistido ano a ano, podendo ser editadas em caso de mudanças.
                  </li>
                  <li>
                    Os saldos de cada revista devem ser cadastrados após a criação da nova licitação.
                  </li>
                  <li>
                    Os limites de cada revista devem er cadastrados após a criação de uma nova licitação.
                  </li>
                  <li>
                    É importante que todos os dados da licitação, da empresa (incluindo a relação com os serviços prestados), da revista (incluindo saldos e limite) estejam corretos e atualizados antes do momento de criação de novos pedidos.
                  </li>
                  <li>
                    O valor unitário e o prazo de cada serviço podem ser editados até que sejam criados limites para esse serviço.
                  </li>
                </ul>
			  </div>
			</div>
		  </div>

		</div>        

        <div class="container mt-5 col">
            <a href="{{ route('home') }}" class="btn btn-secondary"><i class="fas fa-chevron-left"></i>
                Voltar</a>
        </div>
    
    </div>
</div>

@endsection

@section('javascripts_bottom')
<script>
    $('.collapse').collapse('hide');
</script>    
@endsection