@extends('layouts.app')

@section('content')
  <div class="card">
    <div class="card-header h4">
      Arquivo README.md
    </div>
    <div class="card-body">
      {!! md2html(file_get_contents(base_path('welcome.md'))) !!}
    </div>
  </div>
@endsection
