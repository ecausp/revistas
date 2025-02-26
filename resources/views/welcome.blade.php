@extends('layouts.app')

@section('content')
  <div class="card">
    <div class="card-header h5">
      Conceito
    </div>
    <div class="card-body">
      <div class="card-body" style="font-size:16px">
        <ol>
          <li>O Financeiro faz uma consulta para serviços de gráfica e editoração, e cria uma Licitação. Na conclusão obtém a Empresa vencedora e cadastra no sistema Gráfica o valor total licitado, vigência, distribuição dos valores entre as revistas, tipos de serviços, quantidades, valores, contato e credenciais de acesso desta Empresa.</li>
          <li>A Revista recebe notificação da conclusão da Licitação;</li>
          <li>A Revista cria o pedido de Serviço, visualiza as tabelas de valores contratados, recursos financeiros disponíveis e gerencia os pedidos;</li>
          <li>A Empresa verifica se os serviços solicitados nos pedidos estão de acordo;</li>
          <li>O Financeiro verifica se os valores estão de acordo e faz o empenho;</li>
          <li>O Sistema debita o valor dos serviços, atualiza o saldo da Revista e notifica a Empresa para execução dos serviços;</li>
          <li>A Revista confere e recebe o serviço;</li>
          <li>O Sistema notifica o Financeiro para pagamento;</li>
          <li>O Financeiro efetua o pagamento.</li> 
        </ol>
      </div>
    </div>   
  </div>
@endsection
