<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #eee; border-radius: 8px; background-color: #fafafa;">
  <h1 style="color: #333; font-size: 22px; margin-bottom: 10px;">Pedido #{{ $pedido->id }}</h1>

  <p style="margin: 5px 0;"><strong>Revista:</strong> {{ $pedido->revista->name }}</p>
  <p style="margin: 5px 0;"><strong>Status atual:</strong> <span style="color: #26a269">{{ $pedido->status->label() }}</span></p>
  <p>A Notificação de Fornecimento deste pedido, bem como os Empenhos dos serviços foram emitidos.</p>
 

  <hr style="margin: 20px 0; border: none; border-top: 1px solid #ddd;">

  <p style="color: #d9534f; font-weight: bold; margin: 10px 0;">
    Não responda este e-mail!
  </p>
  <p style="margin: 10px 0; color: #555;">
    Acesse o <a href={{ config('app.url')}} style="color: #007BFF; text-decoration: none; font-weight: bold;">
      Sistema de Orçamento das Revistas
    </a> para verificar os detalhes deste pedido.
  </p>

  <hr style="margin: 20px 0; border: none; border-top: 1px solid #ddd;">

  <p style="font-size: 14px; color: #999; text-align: center;">
    Enviado automaticamente pelo sistema em {{ now()->format('d/m/Y H:i') }}.
  </p>
</div>
