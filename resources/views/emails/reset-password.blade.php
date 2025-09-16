<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #eee; border-radius: 8px; background-color: #fafafa;">
    <h1 style="color: #333; font-size: 22px; margin-bottom: 10px;">Se você está recebendo este e-mail porque recebemos um pedido de redefinição de senha para sua conta.</h1>
    <p style="margin: 5px 0;"><strong>Clique no link abaixo para redefinir sua senha:</strong></p>
    <p style="margin: 5px 0;"><strong><a href="{{ $resetUrl }}"  style="color: #007BFF; text-decoration: none; font-weight: bold;">Redefinir Senha</a></strong></p>
    <hr style="margin: 20px 0; border: none; border-top: 1px solid #ddd;">

  <p style="color: #d9534f; font-weight: bold; margin: 10px 0;">
    Não responda este e-mail!
  </p>

  <hr style="margin: 20px 0; border: none; border-top: 1px solid #ddd;">

  <p style="font-size: 14px; color: #999; text-align: center;">
    Enviado automaticamente pelo sistema em {{ now()->format('d/m/Y H:i') }}.
  </p>
</div>
