@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header h5">Cadastrar Nova Ajuda</div>
        <div class="card-body">
            
            <form action="{{ route('ajuda.store') }}" method="POST">
                @csrf <div class="mb-3">
                    <label class="form-label">Categoria</label>
                    <select name="categoria" class="form-control">
                        <option value="financeiro">Financeiro</option>
                        <option value="empresa">Empresa</option>
                        <option value="revista">Revista</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Descrição</label>
                    <input type="text" name="descricao" class="form-control" placeholder="Ex: Como emitir boleto">
                </div>

                <div class="mb-3">
                    <label class="form-label">URL do Vídeo</label>
                    <input type="text" name="url" class="form-control" placeholder="Cole o link do YouTube aqui">
                </div>

                <button type="submit" class="btn btn-primary">Salvar</button>
            </form>
        </div>
    </div>
</div>
@endsection