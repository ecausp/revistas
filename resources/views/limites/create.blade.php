<!-- resources/views/limites/create.blade.php -->
@extends('layouts.app')

@section('content')

<div class="card">
    <div class="card-header h5">
        Criar limite
    </div>
    <div class="card-body">
        @can('admin')
        <form action="{{ route('limites.store') }}" method="POST">
            @csrf
            @include('limites.partials.forms')
        </form>
        @endcan
    </div>
</div>
@endsection


@section('styles')
@parent
<link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
@endsection

@section('javascripts_bottom')
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
@parent
@endsection