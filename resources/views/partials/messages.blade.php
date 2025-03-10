@if ($errors->any())
    @foreach ($errors->all() as $error)
        <div class="alert alert-danger">
            {{$error}}
        </div>
    @endforeach
@endif

@if (session()->has('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if (session()->has('danger'))
    <div class="alert alert-danger">
        {{ session('danger') }}
    </div>
@endif

@section('javascripts_bottom')
  @parent
  <script>
    $(document).ready(function() {
        $(".alert-success").fadeTo(3000, 500).slideUp(500, function(){
            $(".alert-success").slideUp(500);
        });
        $(".alert-danger").fadeTo(3000, 500).slideUp(500, function(){
            $(".alert-danger").slideUp(500);
        });
    });
  </script>
@endsection