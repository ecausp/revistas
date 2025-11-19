@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Editar Ajuda</h1>
    
    <form action="{{ route('ajuda.update', $ajuda->id) }}" method="POST">
        @csrf
        @method('PUT') <div class="mb-3">
            <label>Categoria</label>
            <select name="categoria" class="form-control">
                <option value="financeiro">Financeiro</option>
                <option value="empresa">Empresa</option>
                <option value="revista">Revista</option>
                <option value="admin">Admin</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Descrição</label>
            <input type="text" name="descricao" class="form-control" value="{{ $ajuda->descricao }}">
        </div>

        <div class="mb-3">
            <label>URL</label>
            <input type="text" name="url" class="form-control" value="{{ $ajuda->url }}">
        </div>

        <button type="submit" class="btn btn-primary">Atualizar (Evoluir)</button>
    </form>
</div>

<hr>
    
    <div class="d-flex justify-content-between">
        <a href="{{ route('ajuda.index') }}" class="btn btn-secondary">Voltar</a>

        <form action="{{ route('ajuda.destroy', $ajuda->id) }}" method="POST" onsubmit="return confirm('Tem certeza que quer dar esse Last Hit?');">
            @csrf
            @method('DELETE') <button type="submit" class="btn btn-danger">
                Excluir (Evanesco)
            </button>
        </form>
    </div>
    
@endsection