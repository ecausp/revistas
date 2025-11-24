<!-- resources/views/empresas/ajuda.blade.php -->
@extends('layouts.app')



@section('content')


<div class="card">
    <div class="card-header h5">
        Ajudas
    </div>
    <div class="card-body">
        AQUI ESTARÃO OS TUTORIAIS EM VÍDEO E O PASSO A PASSO EM TEXTO!
        <div class="container mt-5 col">

            <a href="{{ route('empresas.index') }}" class="btn btn-secondary"><i class="fas fa-chevron-left"></i>
                Voltar</a>

        </div>
    </div>
</div>
@endsection