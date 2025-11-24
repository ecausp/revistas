@extends('layouts.app')
@section('content')

<div class="card">
    <div class="card-header h5">
        Criar Ajuda
    </div>
    <div class="card-body">
        @can("admin-financeiro")
        <form action="{{ route('ajuda.store') }}" method="POST">
            @csrf
            @include('ajuda.partials.forms')
        </form>
        @endcan
    </div>
</div>
@endsection