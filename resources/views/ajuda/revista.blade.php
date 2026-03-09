<!-- resources/views/empresas/ajuda.blade.php -->
@extends('layouts.app')

@section('content')

<div class="card">
    <div class="card-header h5">
        Ajudas para a Revista
    </div>

    <div class="card-body">

		<div class="accordion" id="accordionAjuda">
		
      <div class="card">
			<div class="card-header" id="heading1">
			  <h2 class="mb-0">
				<button class="btn btn-link btn-block text-left" type="button" data-toggle="collapse" data-target="#collapse0" aria-expanded="true" aria-controls="collapse0">
				  1. Como indicar um Solicitante
				</button>
			  </h2>
			</div>
			<div id="collapse0" class="collapse" aria-labelledby="heading1" data-parent="#accordionAjuda">
			  <div class="card-body">
                <ol>
                  <li>
                    No menu superior, clique em <span class="badge badge-light p-2"><i class="fas fa-tools" aria-hidden="true"> </i> Financeiro</span> para abrir o menu.
                  </li>
                  <li>
                    Abaixo do item <span class="badge badge-light p-2"><i class="fas fa-book" aria-hidden="true"> </i> Revistas</span>, selecione <span class="badge badge-light p-2"><i class="fas fa-list-ul" aria-hidden="true"> </i> Listar</span> para abrir a lista de Revistas.</li>
                  </li>
                  <li>
                    Nos botões de ações clique no botão <span class="badge badge-primary p-2"><i class="fas fa-user" aria-hidden="true"> </i></span> para acessar a página de Cadastro de Solicitantes.
                  </li>
                  <li>
                    Preencha o nome do Solicitante que deseja cadastrar no campo correspondente e o sistema deve preencher automaticamente as outras informações. Caso o Solicitante desejado não seja encontrado, entre em contato com o suporte.
                  </li>
                  <li>
                    Ao finalizar, basta clicar no botão <span class="badge badge-primary p-2"><i class="fas fa-save" aria-hidden="true"></i> Cadatrar</span> para salvar o novo Solicitante da Revista.
                  </li>
                </ol>           
			  </div>
			</div>
		  </div>

      <div class="card">
			<div class="card-header" id="heading1">
			  <h2 class="mb-0">
				<button class="btn btn-link btn-block text-left" type="button" data-toggle="collapse" data-target="#collapse1" aria-expanded="true" aria-controls="collapse1">
				  2. Como solicitar um Pedido
				</button>
			  </h2>
			</div>
			<div id="collapse1" class="collapse" aria-labelledby="heading1" data-parent="#accordionAjuda">
			  <div class="card-body">
                <ol>
                  <li>
                    No menu superior, clique em <span class="badge badge-light p-2"><i class="fas fa-tools" aria-hidden="true"> </i> Pedidos</span> para abrir o menu de Pedidos
                  </li>
                  <li>
                    Abaixo do item <span class="badge badge-light p-2"><i class="fas fa-shopping-cart" aria-hidden="true"> </i> Pedidos</span>, selecione <span class="badge badge-light p-2"><i class="fas fa-plus-circle" aria-hidden="true"> </i> Cadastrar</span> para abrir a página de cadastro de um novo Pedido.</li>
                  </li>
                  <li>
                    Nessa página, há um campo onde é possível selecionar os serviços que estarão inclusos no pedido a ser feito.
                  </li>
                  <li>
                    Uma vez selecionados os serviços, aparecerá uma tabela com os detalhes de cada um deles, assim como um campo para preencher a quantidade de cada serviço a ser executada nesse pedido, respeitando o que foi definido pelo Financeiro no Limite desse serviço para a revista;
                  </li>
                  <li>
                    Há um campo caso o solicitante queira adicionar observações ao seu pedido.
                  </li>
                  <li>
                    Abaixo, há também a opção de anexar arquivos ao pedido, basta clicar no botão <span class="badge badge-light p2"> Procurar</span> e então o usuário poderá selecionar os arquivos que deseja anexar, respeitando as limitações de tamanho e formato do arquivo
                  </li>
                  <li>
                    Cada arquivo deve conter uma descrição.
                  </li>
                  <li>
                    Ao finalizar, basta clicar no botão <span class="badge badge-primary p-2"><i class="fas fa-save" aria-hidden="true"></i> Salvar</span> para salvar.
                  </li>
                </ol>           
			  </div>
			</div>
		  </div>
		  
		  <div class="card">
			<div class="card-header" id="heading2">
			  <h2 class="mb-0">
				<button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#collapse2" aria-expanded="false" aria-controls="collapse2">
				  3. Como Acompanhar o Andamento do Pedido
				</button>
			  </h2>
			</div>
			<div id="collapse2" class="collapse" aria-labelledby="heading2" data-parent="#accordionAjuda">
			  <div class="card-body">
                <ol>
                  <li>
                    No menu superior, clique em <span class="badge badge-light p-2"><i class="fas fa-tools" aria-hidden="true"> </i> Pedidos</span> para abrir o menu de Pedidos
                  </li>
                  <li>
                    Abaixo do item <span class="badge badge-light p-2"><i class="fas fa-shopping-cart" aria-hidden="true"> </i> Pedidos</span>, selecione <span class="badge badge-light p-2"><i class="fas fa-plus-circle" aria-hidden="true"> </i> Cadastrar</span> para abrir a página de cadastro de um novo Pedido.</li>
                  </li>
                    <li>
                      Nos botões de ações, o usuário pode clicar em <span class="badge badge-primary p-2"><i class="fas fa-eye" aria-hidden="true"> </i></span> para Visualizar o pedido, ou clicar no botão <span class="badge badge-primary p-2"><i class="fas fa-edit" aria-hidden="true"> </i></span> para Editar o pedido, caso seja necessário.
                    </li>
                    <li>
                      Na página de visualização do pedido, podem ser vistas informaçoes como:
                        <ul>
                          <li>
                            um gráfico mostrando o status atual do pedido
                          </li>
                          <li>
                            um cabeçalho com detalhes da revista e do Solicitante que fez o pedido
                          </li>
                          <li>
                            uma tabela com o detalhamento dos serviços incluídos no pedido
                          </li>
                          <li>
                            os arquivos anexados ao pedido
                          </li>
                          <li>
                            o histórico detalhado da evolução do pedido
                          </li>
                        </ul> 
                    </li>
                </ol>
			  </div>
			</div>
		  </div>
		  
		  <div class="card">
			<div class="card-header" id="heading3">
			  <h2 class="mb-0">
				<button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#collapse3" aria-expanded="false" aria-controls="collapse3">
				  4. Como interagir com a Empresa durante o andamento do Pedido de Serviços
				</button>
			  </h2>
			</div>
			<div id="collapse3" class="collapse" aria-labelledby="heading3" data-parent="#accordionAjuda">
			  <div class="card-body">
                <ul>
                    <li>
                      Se tudo estiver de acordo com o pedido, basta que o usuário clique no botão <span class="badge badge-success p-2"><i class="fas fa-glasses" aria-hidden="true"></i> Enviar</span> para que o pedido seja encaminhado para a empresa.
                    </li>
                    <li>
                      Caso a empresa e o financeiro aprovem o pedido, ele passará para o estágio de execução. E uma vez que a execução esteja concluída, então cabe ao editor/solicitante da revista fazer a conferência do material e, caso seja aprovado, clicar no botão <span class="badge badge-info p-2"><i class="fas fa-user-check" aria-hidden="true"> </i> Enviar para Pagmento</span> para que o setor Financeiro efetue o pagamento pelo serviço.
                    </li>
                    <li>
                      Na etapa de Conferência, caso haja algum erro com o Pedido, é possível fazer o retorno para a Empresa usando o botão <span class="badge badge-success p-2"><i class="fas fa-undo-alt" aria-hidden="true"> </i> Devolver</span>
                    </li>
                    <li>
                      Durante todas as etapas do processo de evolução do Pedido, há um chat disponível na página de visualização do Pedido onde todos os usuários envolvidos (responsáveis pela Revista, contatos da Empresa e o setor Financeiro) podem se comunicar.
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