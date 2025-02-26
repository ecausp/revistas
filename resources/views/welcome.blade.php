@extends('layouts.app')

@section('content')
  <div class="card">
    <div class="card-header h4">
      Sistema Gráfica ECA/USP
    </div>
    <div class="card-body">
      <div class="card-header h5">
        Funcionalidades
        <div class="card-body" style="font-size:16px">
          <p>
            O Financeiro faz uma consulta para serviços de gráfica e editoração, e cria uma Licitação. Na conclusão obtém a Empresa vencedora e cadastra no sistema Gráfica o valor total licitado, vigência, distribuição dos valores entre as revistas, tipos de serviços, quantidades, valores, contato e credenciais de acesso desta Empresa.
          <br>
            A Revista recebe notificação da conclusão da Licitação;
            <br>
            A Revista cria o pedido de Serviço, visualiza as tabelas de valores contratados, recursos financeiros disponíveis e gerencia os pedidos;
            <br>
          A Empresa verifica se os serviços solicitados nos pedidos estão de acordo;
          <br>
            O Financeiro verifica se os valores estão de acordo e faz o empenho;
          <br>
            O Sistema debita o valor dos serviços, atualiza o saldo da Revista e notifica a Empresa para execução dos serviços;
          <br>  
            A Revista confere e recebe o serviço;
          <br>
            O Sistema notifica o Financeiro para pagamento;
          <br>
            O Financeiro efetua o pagamento; 
          </p>
        </div>
      </div>  
    </div>
  </div>
@endsection
