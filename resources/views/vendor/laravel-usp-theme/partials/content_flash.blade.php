@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
    </div>
@endif

<div class="flash-message">
    @foreach (['danger', 'warning', 'success', 'info'] as $msg)
        @php
            $message = Session::get($msg, Session::get('alert-' . $msg));
        @endphp
        @if ($message)
            <div class="alert alert-{{ $msg }} alert-dismissible fade show" role="alert">
                {{ $message }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
            </div>
            @php
                Session::forget([$msg, 'alert-' . $msg]);
            @endphp
        @endif
    @endforeach

    @if (Session::has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ Session::get('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
        </div>
        @php
            Session::forget('error');
        @endphp
    @endif
</div>
