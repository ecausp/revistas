<link rel="stylesheet" href="{{ asset('css/chat.css') }}">

@if($pedido->status == 'em_analise_empresa')
<div class="card mt-4">
    <div class="card-header h5">
        Chat
    </div>
    <div class="card-body" style="">
        @forelse($pedido->chats as $chat)
            @php
            if ($chat->user_id === auth()->id()) {
                $class = 'user';
                $align = 'text-end';
            } elseif ($pedido->servicos->flatMap(fn($s) => $s->empresas)->flatMap(fn($e) => $e->contatos->pluck('user_id'))->contains($chat->user_id)) {
                $class = 'contato';
                $align = 'text-start';
            } else {
                $class = 'solicitante';
                $align = 'text-start';
            }
        @endphp

            <div class="mb-3 {{ $align }}">
                <div class="small">
                    <strong>{{ $chat->user->name }}</strong> - {{ $chat->created_at->format('d/m/Y H:i') }}
                </div>
                <div class="chat-message {{ $class }}">
                    {{ $chat->message }}
                </div>
            </div>
        @empty
            <p class="text-muted">Nenhuma mensagem ainda.</p>
        @endforelse
    </div>
    <div class="card-footer">
        <form action="{{ route('chat.store', ['pedido' => $pedido->id]) }}" method="POST">
            @csrf
            <input type="hidden" name="pedido_id" value="{{ $pedido->id }}">
            <div class="input-group">
                <textarea name="message" class="form-control" placeholder="Escreva sua mensagem..." required></textarea>
            </div>
            <button class="btn btn-primary mt-2" type="submit">Enviar</button>
        </form>
    </div>
</div>
@endif
