@extends('layouts.app')

@section('content')

<div class="card">
    <div class="card-header h5">
        Ajuda
    </div>
    <div class="card-body">
        <p><strong>{{ $ajuda->descricao }}</strong></p>
        <span class="badge mb-3">{{ $ajuda->categoria }}</span>
        
        <<div class="embed-responsive embed-responsive-16by9" style="width: 50%; margin: 0 auto;">
    <iframe 
        class="embed-responsive-item" 
        src="https://www.youtube.com/embed/{{ $ajuda->url }}" 
        allowfullscreen>
    </iframe>
</div>

        <hr>

        <div class="d-flex justify-content-between">
            <a href="{{ route('ajuda.index') }}" class="btn btn-secondary">
                Voltar
            </a>
            
            </div>
    </div>
</div>
@endsection