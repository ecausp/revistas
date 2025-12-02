@extends('laravel-usp-theme::master')

{{-- Blocos do laravel-usp-theme --}}
{{-- Ative ou desative cada bloco --}}

{{-- Target:card-header; class:card-header-sticky --}}
@include('laravel-usp-theme::blocos.sticky')

{{-- Target: button, a; class: btn-spinner, spinner --}}
@include('laravel-usp-theme::blocos.spinner')

{{-- Target: table; class: datatable-simples --}}
{{-- MANTIDO: O bloco original da empresa permanece aqui para carregar as configs padrões --}}
@include('laravel-usp-theme::blocos.datatable-simples')

{{-- Fim de blocos do laravel-usp-theme --}}

@section('title')
@parent
@endsection

@include('partials.aviso')

@section('styles')
@parent
{{-- CSS da Extensão Responsive --}}
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">

<style>
    /* Seus estilos personalizados */
</style>
@endsection

@section('javascripts_bottom')
@parent
{{-- JS da Extensão Responsive --}}
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

<script>
    // Script Inteligente: Aplica responsividade e lê configurações dos componentes
    $(document).ready(function() {
        var tableSelector = '.datatable-simples';

        // Só roda se o tema USP já tiver criado as tabelas
        if ($.fn.DataTable.isDataTable(tableSelector)) {
            
            // Itera sobre cada tabela individualmente para respeitar configurações diferentes
            $(tableSelector).each(function() {
                var table = $(this);
                
                // 1. Clona as configurações originais do tema (tradução, botões, etc.)
                var settings = table.DataTable().init();
                
                // 2. Adiciona a responsividade (Sempre ativado)
                settings.responsive = true;
                settings.autoWidth = false;

                // 3. LÊ OS PARAMETROS DOS COMPONENTES (x-table vs x-table-simples)
                // Se o componente tiver data-searching="false", desliga a busca
                if (table.data('searching') === false) settings.searching = false;
                
                // Se tiver data-paging="false", desliga a paginação
                if (table.data('paging') === false) settings.paging = false;
                
                // Se tiver data-info="false", desliga o rodapé "Mostrando 1 de X"
                if (table.data('info') === false) settings.info = false;
                
                // Se tiver data-ordering="false", desliga a ordenação
                if (table.data('ordering') === false) settings.ordering = false;

                // 4. Destrói a tabela antiga (evita erro de reinicialização)
                table.DataTable().destroy();

                // 5. Recria a tabela com as configurações combinadas
                table.DataTable(settings);
            });
        }
    });
</script>
@endsection