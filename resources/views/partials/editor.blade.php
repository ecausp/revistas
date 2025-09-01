@if (isset($editor))
<small class="text-muted">Atualizado em {{ date_format($updated_at, 'd/m/Y H:i') }}
    <br />por {{ $editor->name }} - {{ $editor->email }}</small>
@endif