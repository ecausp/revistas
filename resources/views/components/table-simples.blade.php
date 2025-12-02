<table {{ $attributes->merge([
    'class' => 'table table-hover datatable-simples responsive no-footer table-striped',
    'data-searching' => 'false',
    'data-paging' => 'false',
    'data-info' => 'false',
    // 'data-ordering' => 'false' // Descomente se quiser sem ordenação também
]) }}>
    {{ $slot }}
</table>