<!-- resources/views/empresas/edit.blade.php -->
@extends('layouts.app')

@section('content')

<div class="card">
    <div class="card-header h5">
        Editar Empresa
    </div>
    <div class="card-body">

        @can('admin')
        <form action="{{ route('empresas.update', $empresa) }}" method="POST">
            @csrf
            @method('PUT')
            @include('empresas.partials.forms')
        </form>
    </div>
    @endcan
</div>
@endsection



@section('styles')
@parent
<link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
@endsection

@section('javascripts_bottom')
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>


<script>
$(document).ready(function() {
    // Formato cnpj
    $('.cnpj').mask('00.000.000/0000-00', {
        reverse: true
    });
});
</script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const modal = new bootstrap.Modal(document.getElementById('servicoModal'));
    document.getElementById('servico-modal').addEventListener('click', () => {
        modal.show();
    });
});
</script>



@endsection