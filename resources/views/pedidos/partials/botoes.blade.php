<div class="container mt-5 col pb-3 pl-3">

    <!-- se o status do pedido for "em elaboração": -->
    @if($pedido->status == "em_elaboracao")
        @if(auth()->user()->can('acesso-revista') && !auth()->user()->can('admin') && !auth()->user()->hasRole('financeiro'))
            <a href="{{ route('pedidos.edit', $pedido) }}" class="btn btn-primary"><i class="fas fa-edit"></i>Editar</a>
        @endif

        @if((auth()->user()->can('acesso-revista') && !auth()->user()->can('admin')) || auth()->user()->hasRole('financeiro'))
            <form action="{{ route('pedidos.analise-empresa', $pedido) }}" method="POST" class="d-inline">
            @csrf
                <button type="submit" class="btn btn-success" title="Enviar para análise da empresa.">
                    <i class="fas fa-glasses"></i> Enviar
                </button>
            </form>
        @endif

        @if((auth()->user()->can('admin') || auth()->user()->can('acesso-revista')) || auth()->user()->hasRole('financeiro'))
            @include('pedidos.partials.delete')
        @endif
    @endif


    <!-- se o status do pedido for "em analise empresa": -->
    @if($pedido->status == "em_analise_empresa")
        @if(auth()->user()->can('acesso-empresa') && !auth()->user()->can('admin') && ! auth()->user()->hasRole('financeiro'))
            <button type="button" class="btn btn-warning" title="Devolver para a revista" data-bs-toggle="modal" data-bs-target="#modalDevolver">
                <i class="fas fa-undo-alt"></i> Devolver
            </button>

            <form action="{{ route('pedidos.analise-financeiro', $pedido) }}" method="POST" class="d-inline">
            @csrf
                <button type="submit" class="btn btn-info" title="Enviar para análise do financeiro.">
                    <i class="fas fa-glasses"></i> Aprovar
                </button>
            </form>
        @endif
    @endif

    <!-- se o status do pedido for "em analise financeiro": -->
    @if($pedido->status == "em_analise_financeiro")
        @if(auth()->user()->hasRole('financeiro'))
            <button type="button" class="btn btn-warning" title="Devolver para a revista" data-bs-toggle="modal" data-bs-target="#modalDevolver">
                <i class="fas fa-undo-alt"></i> Devolver
            </button>

            <button type="button" class="btn btn-success" title="Enviar para execução" data-bs-toggle="modal" data-bs-target="#modalAprovar">
                <i class="fas fa-tools"></i> Aprovar
            </button>
        @endif
    @endif

    <!-- se o status do pedido for "em execucao": -->
    @if($pedido->status == "em_execucao")
        <!-- se o usuário for do financeiro: -->
        @if(auth()->user()->hasRole('financeiro'))
            <button id="btnPdf" class="btn btn-primary no-print">
                <i class="fas fa-file-pdf"></i> Salvar em PDF
            </button>
        <!-- se o usuário for da empresa: -->
        @elseif(auth()->user()->can('acesso-empresa') && !auth()->user()->can('admin') && ! auth()->user()->hasRole('financeiro'))
            <!-- Botão que envia o pedido para conferência-->
            <form action="{{ route('pedidos.conferencia', $pedido) }}" method="POST" class="d-inline">
            @csrf
                <button type="submit" class="btn btn-warning" title="Enviar para conferência.">
                    <i class="fas fa-user-check"></i> Enviar para Conferência
                </button>
            </form>
          @endif
    @endif

    <!-- se o status do pedido for "em conferencia": -->
    @if($pedido->status == "em_conferencia")
        @if(auth()->user()->can('acesso-revista') && !auth()->user()->can('admin') && ! auth()->user()->hasRole('financeiro'))
            <button type="button" class="btn btn-success" title="Devolver para a empresa" data-bs-toggle="modal" data-bs-target="#modalDevolver">
                <i class="fas fa-undo-alt"></i> Devolver
            </button>

            <form action="{{ route('pedidos.em-pagamento', $pedido) }}" method="POST" class="d-inline">
            @csrf
                <button type="submit" class="btn btn-info" title="Enviar para Pagamento.">
                    <i class="fas fa-user-check"></i> Enviar para Pagamento
                </button>
            </form>
        @endif
    @endif

    <!-- se o status do pedido for "em pagamento": -->
    @if($pedido->status == "em_pagamento")
        @if(auth()->user()->hasRole('financeiro'))
            <form action="{{ route('pedidos.concluido', $pedido) }}" method="POST" class="d-inline">
            @csrf
                <button type="submit" class="btn btn-primary" title="Concluir Pedido.">
                    <i class="fas fa-check-circle"></i> Concluido
                </button>
            </form>
        @endif
    @endif

    <a href="{{ route('pedidos.index') }}" class="btn btn-secondary no-print"><i class="fas fa-chevron-left"></i> Voltar</a>

</div>