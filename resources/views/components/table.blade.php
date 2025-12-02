<table {{ $attributes->merge(['class' => 'table table-hover datatable-simples dt-fixed-header dt-paging-50 responsive no-footer table-striped']) }}>
    {{ $slot }}
</table>