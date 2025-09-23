@if (config('revistas.aviso'))
  <div class="alert alert-{{ config('revistas.avisoTipo') }} alert-dismissible text-center rounded-0 m-0 p-2 border-0 mb-4" role="alert">
    {{ config('revistas.aviso') }}
  </div>
@endif