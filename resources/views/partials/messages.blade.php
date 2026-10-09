{{-- ERRORS --}}
@if ($errors->any())
    @foreach ($errors->all() as $error)
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ $error }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Fechar">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endforeach
@endif

{{-- SUCCESS --}}
@if (session()->has('success'))
    <div class="alert alert-success alert-dismissible fade show auto-hide" role="alert">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Fechar">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

{{-- DANGER --}}
@if (session()->has('danger'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('danger') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Fechar">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

{{-- WARNING --}}
@if (session()->has('warning'))
    <div class="alert alert-warning alert-dismissible fade show auto-hide" role="alert">
        {{ session('warning') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Fechar">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

{{-- INFO --}}
@if (session()->has('info'))
    <div class="alert alert-info alert-dismissible fade show auto-hide" role="alert">
        {{ session('info') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Fechar">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

@section('javascripts_bottom')
    @parent
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            // Alerts que devem sumir automaticamente (success, info, warning)
            const autoHideAlerts = document.querySelectorAll('.auto-hide');

            autoHideAlerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.transition = 'opacity 0.5s';
                    alert.style.opacity = '0';
                    setTimeout(() => alert.remove(), 500);
                }, 6000); // 6 segundos
            });

        });
    </script>
@endsection
