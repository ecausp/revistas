
<div class="container mt-5 col">

    <!-- se o status do pedido for "em elaboração": -->
    @if($pedido->status == "em_elaboracao")
        <!-- se o usuário for solicitante da revista: -->
        @if(auth()->user()->can('acesso-revista') && !auth()->user()->can('admin') && ! auth()->user()->hasRole('financeiro'))
            
            <!-- [1] - editar -->
            <a href="{{ route('pedidos.edit', $pedido) }}" class="btn btn-primary"><i class="fas fa-edit"></i>Editar</a>
        
            <!-- [3] - enviar para empresa -->
            <form action="{{ route('pedidos.analise-empresa', $pedido) }}" method="POST" class="d-inline">
            @csrf
                <button type="submit" class="btn btn-success" title="Enviar para análise da empresa.">
                    <i class="fas fa-glasses"></i> Enviar
                </button>
            </form>
        @endif

        <!-- se o usuario for solicitante da revista, admin ou financeiro: -->
        @if((auth()->user()->can('admin') || auth()->user()->can('acesso-revista')) || auth()->user()->hasRole('financeiro'))
            
            <!-- [4] - deletar -->
            @include('pedidos.partials.delete')
        
        @endif

    @endif


    <!-- se o status do pedido for "em analise empresa": -->
    @if($pedido->status == "em_analise_empresa")
        <!-- se o usuário for contato da empresa: -->
        @if(auth()->user()->can('acesso-empresa') && !auth()->user()->can('admin') && ! auth()->user()->hasRole('financeiro'))
            <!-- [1] - devolver o pedido-->
            <!-- Botão que abre o modal para inserir o motivo da devolução-->
            <button type="button" class="btn btn-warning" title="Devolver para a revista" data-bs-toggle="modal" data-bs-target="#modalDevolver">
                <i class="fas fa-undo-alt"></i> Devolver
            </button>

            <!-- [2] - aprovar o pedido-->
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
        <!-- se o usuário for do financeiro: -->
        @if(auth()->user()->hasRole('financeiro'))
            <!-- [1] - devolver o pedido-->
            <!-- Botão que abre o modal para inserir o motivo da devolução-->
            <button type="button" class="btn btn-warning" title="Devolver para a revista" data-bs-toggle="modal" data-bs-target="#modalDevolver">
                <i class="fas fa-undo-alt"></i> Devolver
            </button>

            <!-- [2] - aprovar o pedido-->
            <!-- Botão que abre o modal para inserir os números -->
            <button type="button" class="btn btn-success" title="Enviar para execução" 
                    data-bs-toggle="modal" data-bs-target="#modalAprovar">
                <i class="fas fa-tools"></i> Aprovar
            </button>
        
        @endif
    @endif


    <a href="{{ route('pedidos.index') }}" class="btn btn-secondary"><i class="fas fa-chevron-left"></i>Voltar</a>

</div>