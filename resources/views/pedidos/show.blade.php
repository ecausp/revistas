@extends('layouts.app')
@section('content')

<div class="card">
    <div class="card-header h5">
        <div class="d-flex align-items-center">
            Pedido {{ $pedido->id }}
            <i class="{{ $pedido->status->icon() }} ml-4 mr-2" style="{{ $pedido->status->icon_style() }}"></i>
            <span class="text-secondary" style="font-size: .9rem; font-style: italic;">{{ $pedido->status->label() }}</span>
        </div>
    </div>

    <div class="card-body">
        @include('pedidos.partials.status')
        
        @include('pedidos.partials.cabecalho')
        <p><strong>Observações:</strong> {{ $pedido->observacoes }}</p>

        @include('pedidos.partials.arquivos')

        @include('pedidos.partials.historico')
    </div>
</div>
@endsection



@section('javascripts_bottom')
    @parent
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <!-- Tradução para português do Brasil -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/i18n/pt-BR.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>

    <script>
    $(document).ready(function () {
        $('.milhares-mask').mask('#.##0', { reverse: true });

        $('#formDevolver').on('submit', function () {
        $('#formDevolver .milhares-mask').each(function () {
            let val = $(this).val().replace(/\./g, '');
            $(this).val(val);
        });
        });
    });
    </script>

    <script>
    document.getElementById('btnPdf')?.addEventListener('click', function () {
        // Esconde o chat para impressão
        document.querySelector('#chatContainer')?.classList.add('no-print');

        window.print();

        // Volta a exibir o chat depois da impressão
        document.querySelector('#chatContainer')?.classList.remove('no-print');
    });
    </script>

    <style>
    @media print {
        .no-print {
            display: none !important;
        }
    }
    </style>
@endsection