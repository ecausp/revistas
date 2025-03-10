<!-- resources/views/licitacoes/edit.blade.php -->
@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header h5">
        Editar Licitação
    </div>
    @can('admin')
    <div class="card-body">
        <form action="{{ route('licitacoes.update', $licitacao) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-1">
                <label for="descricao" class="form-label">Descrição</label>
                <input type="text" class="form-control" name="descricao" id="descricao"
                    value="{{ old('descricao', $licitacao->descricao) }}" required></input>
            </div>

            <div class="mb-1">
                <label for="ano" class="form-label">Ano</label>
                <input type="month" class="form-control" name="ano" id="ano" value="{{ old('ano', $licitacao->ano) }}" required>
            </div>

            <div class="mb-1">
                <label for="valor_total" class="form-label">Valor Total</label>
                <input type="text" class="form-control money" name="valor_total" id="valor_total"
                    value="{{ old('valor_total', $licitacao->valor_total) }}" placeholder="000.000.000.000.000,00" required>
            </div>

            <div class="container mt-5 col">
                <button type="submit" class="btn btn-primary">Atualizar</button>
                <a href="{{ route('licitacoes.index') }}" class="btn btn-secondary"><i class="fas fa-chevron-left"></i>
                    Voltar</a>
            </div>
        </form>
    </div>
    @endcan
</div>
@endsection

{{-- 
Não encontrei o viewMode years no JQuery Datepicker do Laravel USP Theme.
Precisei utilizar o Bootstrap Datepicker.
TODO: Verificar se o Bootstrap Datepicker funciona com o Laravel USP Theme.  
--}}

@section('styles')
  @parent
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
@endsection

@section('javascripts_bottom')
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
  @parent
  <script>
    $(document).ready(function() {
        $('#ano').datepicker({
            format: "yyyy",         // Exibir apenas o ano
            viewMode: "years",      // Iniciar na visualização de anos
            minViewMode: "years",   // Impedir mudança para meses/dias
            autoclose: true         // Fecha automaticamente ao selecionar o ano
        });

        // Formato moeda
        $('.money').mask('000.000.000.000.000,00', {reverse: true});
    });
  </script>
@endsection