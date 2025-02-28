
<!-- resources/views/licitacoes/create.blade.php -->
@extends('layouts.app')

@section('content')
  <div class="card">
    <div class="card-header h5">
      Criar Licitação
    </div>
    <div class="card-body">
    @can('admin')
      <form action="{{ route('licitacoes.store') }}" method="POST" class="needs-validation" novalidate>
        @csrf
        <div class="mb-1">
          <label for="descricao" class="form-label">Descrição</label>
          <textarea class="form-control" name="descricao" id="descricao" rows="1" required></textarea>
        </div>

        <div class="mb-1">
          <label for="ano" class="form-label">Ano</label>
          <input type="month" class="form-control" name="ano" id="ano" required>
        </div>

        <div class="mb-1">
          <label for="valor_total" class="form-label">Valor Total</label>
          <input type="number" class="form-control" name="valor_total" id="valor_total" required>
        </div>

        <div class="container mt-5 col">
          <button type="submit" class="btn btn-primary">Salvar</button>
          <a href="{{ route('licitacoes.index') }}" class="btn btn-secondary"><i class="fas fa-chevron-left"></i> Voltar</a>

        </div>
      
      </form>

      
    @endcan
    </div>
@endsection
