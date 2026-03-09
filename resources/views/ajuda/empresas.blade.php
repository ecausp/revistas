<!-- resources/views/empresas/ajuda.blade.php -->
@extends('layouts.app')

@section('content')

<div class="card">
    <div class="card-header h5">
        Ajudas para a Empresa
    </div>

    <div class="card-body">

		<div class="accordion" id="accordionAjuda">
		  
		  <div class="card">
			<div class="card-header" id="heading1">
			  <h2 class="mb-0">
				<button class="btn btn-link btn-block text-left" type="button" data-toggle="collapse" data-target="#collapse1" aria-expanded="true" aria-controls="collapse1">
				  1. Acesso ao Sistema
				</button>
			  </h2>
			</div>
			<div id="collapse1" class="collapse" aria-labelledby="heading1" data-parent="#accordionAjuda">
			  <div class="card-body">
                <ol>
                    <li>Na página principal, clique no botão <span class="badge badge-success p-2"><i class="fas fa-sign-in-alt" aria-hidden="true"> </i> Empresa</span></li>
                    <li>Se já tiver definido sua senha, basta entrar com o email e a senha criadas para acessar o sistema.</li>
                    <li>Caso seja o primeiro acesso, clique no botão <span class="badge badge-secondary p-2"><i class="fas fa-question" aria-hidden="true"> </i> Esqueci a Senha</span></li>
                    <ul>
                        <li>Preencha seu e-mail no campo indicado.</li>
                        <li>Clique no botão <span class="badge badge-primary p-2"><i class="fas fa-paper-plane" aria-hidden="true"> </i> Enviar Link</span></li>
                        <li>Se o e-mail indicado tiver sido cadastrado como contato de alguma empresa, você deve receber um link para criar uma senha.</li>
                    </ul>
                    <li>Você também deverá receber um e-mail com o link para definir uma senha quando seu e-mail for cadastrado como contato de uma empresa.</li>
                </ol>
                <div class="video-container">
                    <div class="card-header h5 mb-3">
                        Veja como funciona:
                    </div>
                    <video controls width="50%" class="rounded shadow-md">
                        <source src="{{ asset('videos/ajuda_acesso_empresa.webm') }}" type="video/webm">
                            Seu navegador não suporta a tag de vídeo.
                    </video>
                </div>                
			  </div>
			</div>
		  </div>
		  
		  <div class="card">
			<div class="card-header" id="heading2">
			  <h2 class="mb-0">
				<button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#collapse2" aria-expanded="false" aria-controls="collapse2">
				  2. Visualizar Lista de Pedidos
				</button>
			  </h2>
			</div>
			<div id="collapse2" class="collapse" aria-labelledby="heading2" data-parent="#accordionAjuda">
			  <div class="card-body">
                <ol>
                    <li>No menu superior, clique em <span class="badge badge-light p-2"><i class="fas fa-tools" aria-hidden="true"> </i> Pedidos</span> para abrir o menu de Pedidos</li>
                    <li>Abaixo do item <span class="badge badge-light p-2"><i class="fas fa-shopping-cart" aria-hidden="true"> </i> Pedidos</span>, selecione <span class="badge badge-light p-2"><i class="fas fa-list-ul" aria-hidden="true"> </i> Listar</span></li>
                    <li>Desse modo, você terá acesso à uma tabela com todos os pedidos, cobrindo detalhes como:</li>
                    <ul>
                        <li><strong>Número do Pedido:</strong> um número para identificar o pedido.</li>
                        <li><strong>Revista:</strong> o nome da revista que fez o pedido.</li>
                        <li><strong>Licitação:</strong> a qual licitação pertence o pedido.</li>
                        <li><strong>Status:</strong> status atual do pedido.</li>
                        <li><strong>Número da Notificação de Fornecimento:</strong> número para identificar a Notificação de fornecimento do pedido</li>
                        <li><strong>Ações:</strong> um botão que redireciona para a página individual do pedido.</li>
                    </ul>
                </ol>
			  </div>
			</div>
		  </div>
		  
		  <div class="card">
			<div class="card-header" id="heading3">
			  <h2 class="mb-0">
				<button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#collapse3" aria-expanded="false" aria-controls="collapse3">
				  3. Visualizar um Pedido
				</button>
			  </h2>
			</div>
			<div id="collapse3" class="collapse" aria-labelledby="heading3" data-parent="#accordionAjuda">
			  <div class="card-body">
                <ul>
                    <li>Ao visualizar um pedido, você terá acesso na página às seguintes informações:</li>
                        <ol>
                            <li>Evolução no status do pedido;</li>
                            <li>Cabeçalho com as informações do pedido;</li>
                            <li>Tabela com detalhes dos serviços inclusos no pedido;</li>
                            <li>Tabela com os arquivos anexados ao pedido;</li>
                            <li>Histórico do pedido;</li>
                            <li>Mensagens do pedido;</li>
                            <li>Botões de ação do pedido.</li>
                        </ol>
                </ul>			  
              </div>
			</div>
		  </div>
		  
		  <div class="card">
			<div class="card-header" id="heading4">
			  <h2 class="mb-0">
				<button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#collapse4" aria-expanded="false" aria-controls="collapse4">
				  4. Visualizar Serviços
				</button>
			  </h2>
			</div>
			<div id="collapse4" class="collapse" aria-labelledby="heading4" data-parent="#accordionAjuda">
			  <div class="card-body">
                <ol>
                    <li>No menu superior, clique em <span class="badge badge-light p-2"><i class="fas fa-tools" aria-hidden="true"> </i> Pedidos</span> para abrir o menu de Serviços</li>
                    <li>Abaixo do item <span class="badge badge-light p-2"><i class="fas fa-tasks" aria-hidden="true"> </i> Serviços</span>, selecione <span class="badge badge-light p-2"><i class="fas fa-list-ul" aria-hidden="true"> </i> Listar</span></li>
                    <li>Desse modo, você terá acesso à uma tabela com todos os serviços, cobrindo detalhes como:</li>
                    <ul>
                        <li><strong>Serviço:</strong> a descrição do serviço.</li>
                        <li><strong>Unidade de Medida:</strong> a unidade de medida do serviço.</li>
                        <li><strong>Valor Unitário:</strong> o valor unitário do serviço.</li>
                        <li><strong>Prazo:</strong> o prazo em dias para a conclusão do serviço.</li>
                        <li><strong>Ações:</strong> um botão que redireciona para a página individual do serviço.</li>
                    </ul>
                    <li>Abaixo do item <span class="badge badge-light p-2"><i class="fas fa-tasks" aria-hidden="true"> </i> Serviços</span>, selecione <span class="badge badge-light p-2"><i class="fas fa-cog" aria-hidden="true"> </i> Em Execução</span></li>
                    <li>Desse modo, você terá acesso à uma tabela com os serviços em execução, cobrindo detalhes como:</li>
                    <ul>
                        <li><strong>Número do Pedido:</strong> número que identifica o pedido em qual o serviço está sendo requisitado.</li>
                        <li><strong>Empresa:</strong> nome da empresa que presta o serviço.</li>
                        <li><strong>Serviço:</strong>A descrição do serviço.</li>
                        <li><strong>Número do Empenho:</strong> nũmero do documento de Empenho do serviço.</li>
                        <li><strong>Prazo:</strong> o prazo em dias para a conclusão do serviço.</li>
                    </ul>
                </ol>			  
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