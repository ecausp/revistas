<link rel="stylesheet" href="{{ asset('css/chat.css') }}?v={{ time() }}">

@if($pedido->status != 'em_elaboracao')
<div class="card mt-4">
    <div class="card-header h5">
        Chat
    </div>
    <div class="card-body">
        @forelse($pedido->chats as $chat)
            @php
                if ($chat->user_id === auth()->id()) {
                    $class = 'user';
                    $class_tag = "";
                    $align = 'text-end';
                    $color = "alert alert-primary";
                } 
                elseif ($chat->user->hasRole('financeiro')) {
                    $class = 'financeiro';
                    $class_tag = " - Financeiro";
                    $align = 'text-start';
                    $color = "alert alert-info";
                }
                elseif (isset($contatosIds) && in_array($chat->user_id, $contatosIds)) {
                    $class = 'contato';
                    $class_tag = " - Contato da Empresa";
                    $align = 'text-start';
                    $color = 'alert alert-success';
                }
                else {
                    $class = 'solicitante';
                    $class_tag = " - Solicitante da Revista";
                    $align = 'text-start';
                    $color = "alert alert-warning";
                }
            @endphp

            <div class="mb-3 {{ $align }}">
                <div class="small">
                    <strong>{{ $chat->user->name }}</strong> - {{ $chat->created_at->format('d/m/Y H:i') }} 
                    <span class="text-muted">{{ $class_tag }}</span>
                </div>
                <div class="chat-message {{ $class }} {{ $color }}">
                    {{ $chat->message }}
                </div>
            </div>
        @empty
            <p class="text-muted">Nenhuma mensagem ainda.</p>
        @endforelse
    </div>

    @if($pedido->status != 'concluido')
    <div class="card-footer">
        <form action="{{ route('chat.store', ['pedido' => $pedido->id]) }}" method="POST">
            @csrf
            <div class="input-group">
                <textarea name="message" class="form-control" placeholder="Escreva sua mensagem..." required></textarea>
            </div>
            <button class="btn btn-primary mt-2" type="submit">Enviar</button>
        </form>
    </div>
    @endif
</div>
@endif