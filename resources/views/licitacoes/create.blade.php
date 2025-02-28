
<!-- resources/views/licitacoes/create.blade.php -->
@extends('layouts.app')

@section('content')
  <div class="card">
    <div class="card-header h5">
      Criar Licitação
    </div>
    <div class="card-body">
    @can('admin')
      <form action="{{ route('licitacoes.store') }}" method="POST">
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
        <button type="submit" class="btn btn-primary">Salvar</button>


      </form>

      
    @endcan
    </div>
@endsection
