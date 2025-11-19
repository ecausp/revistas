@extends('layouts.app')

@section('content')


<div class="card">
    <div class="card-header h5">
        Editar Ajuda "{{ $ajuda->descricao }}"
    </div>
    <div class="card-body">

        @can("admin-financeiro")
        <form action="{{ route('ajuda.update', $ajuda) }}" method="POST">
            @csrf
            @method('PUT')
            @include('ajuda.partials.forms')
        </form>

        <div class="d-flex justify-content-between align-items-center">
        
        <a href="{{ route('ajuda.index') }}" class="btn btn-secondary">
            &larr; Voltar
        </a>

        <form action="{{ route('ajuda.destroy', $ajuda->id) }}" method="POST" 
              onsubmit="return confirm('Tem certeza que deseja excluir este vídeo?');">
            @csrf
            @method('DELETE')
            
            <button type="submit" class="btn btn-danger">
                Excluir Ajuda
            </button>
        </form>
        
    </div>

</div>
    </div>
    @endcan
</div>

@endsection
