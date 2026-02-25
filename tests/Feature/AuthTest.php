<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Mail\ResetPasswordMail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    // --- BLOCO 1: LOGIN E LOGOUT (AUTENTICAÇÃO DE EMPRESA) ---

    #[ \PHPUnit\Framework\Attributes\Test ]
    public function usuario_consegue_visualizar_tela_de_login()
    {
        $response = $this->get(route('login_empresa'));

        // O dump salvador
        if ($response->status() !== 200) {
            dump($response->headers->get('Location'));
        }

        $response->assertStatus(200);
    }

    #[ \PHPUnit\Framework\Attributes\Test ]
    public function usuario_consegue_logar_com_credenciais_corretas()
    {
        $password = 'senha_secreta_123';
        $user = User::factory()->create([
            'email' => 'empresa@teste.com',
            'password' => Hash::make($password)
        ]);

        $response = $this->post(route('login_empresa'), [
            'email' => 'empresa@teste.com',
            'password' => $password
        ]);

        $response->assertRedirect(route('home'));
        $this->assertAuthenticatedAs($user);
    }

    #[ \PHPUnit\Framework\Attributes\Test ]
    public function login_deve_falhar_com_senha_incorreta()
    {
        $user = User::factory()->create(['password' => Hash::make('correta')]);

        $response = $this->post(route('login_empresa'), [
            'email' => $user->email,
            'password' => 'errada'
        ]);

        $response->assertRedirect(route('login_empresa'));
        $response->assertSessionHas('danger', 'Credenciais inválidas!');
        $this->assertGuest();
    }

    #[ \PHPUnit\Framework\Attributes\Test ]
    public function logout_deve_limpar_a_autenticacao_e_sessao()
    {
        $user = User::factory()->create();

        // O actingAs simula o usuário logado antes de chamar o logout
        $response = $this->actingAs($user)->get(route('logout'));

        $response->assertRedirect(route('login_empresa'));
        $this->assertGuest();
    }

    // --- BLOCO 2: RECUPERAÇÃO DE SENHA (ESQUECI A SENHA) ---

    #[ \PHPUnit\Framework\Attributes\Test ]
    public function envio_de_link_de_reset_deve_disparar_email_se_usuario_existir()
    {
        Mail::fake();
        $user = User::factory()->create(['email' => 'financeiro@empresa.com']);

        $response = $this->post(route('password.email'), ['email' => 'financeiro@empresa.com']);

        $response->assertSessionHas('success');
        
        // Verifica se o registro de reset foi criado no banco
        $this->assertDatabaseHas('password_resets', [
            'email' => 'financeiro@empresa.com'
        ]);

        // Verifica se a Mailable correta foi enviada para o destino certo
        Mail::assertSent(ResetPasswordMail::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email);
        });
    }

    #[ \PHPUnit\Framework\Attributes\Test ]
    public function nao_deve_enviar_email_para_usuario_inexistente()
    {
        Mail::fake();

        $response = $this->post(route('password.email'), ['email' => 'fantasma@usp.br']);

        $response->assertSessionHas('danger', 'Credenciais inválidas!');
        Mail::assertNothingSent();
    }

    // --- BLOCO 3: REDEFINIÇÃO DE SENHA (RESET PASSWORD) ---

    #[ \PHPUnit\Framework\Attributes\Test ]
    public function usuario_consegue_resetar_senha_com_token_valido()
    {
        $user = User::factory()->create(['password' => Hash::make('velha123')]);
        $token = Str::random(60);

        // Insere o token manualmente como o Controller espera
        DB::table('password_resets')->insert([
            'email' => $user->email,
            'token' => $token,
            'created_at' => Carbon::now()
        ]);

        $response = $this->post(route('password.update'), [
            'token' => $token,
            'email' => $user->email,
            'password' => 'nova_senha_456',
            'password_confirmation' => 'nova_senha_456'
        ]);

        $response->assertRedirect(route('home'));
        $response->assertSessionHas('success', 'Senha redefinida com sucesso!');

        // Verifica se o Hash da nova senha agora bate
        $this->assertTrue(Hash::check('nova_senha_456', $user->fresh()->password));

        // Garante que o token foi limpo do banco para não ser usado de novo
        $this->assertDatabaseMissing('password_resets', ['email' => $user->email]);
    }

    #[ \PHPUnit\Framework\Attributes\Test ]
    public function reset_de_senha_deve_falhar_se_token_for_invalido()
    {
        $user = User::factory()->create();
        
        DB::table('password_resets')->insert([
            'email' => $user->email,
            'token' => 'token-original',
            'created_at' => Carbon::now()
        ]);

        $response = $this->post(route('password.update'), [
            'token' => 'TOKEN-ERRADO-DE-HACKER',
            'email' => $user->email,
            'password' => 'senha123',
            'password_confirmation' => 'senha123'
        ]);

        $response->assertSessionHas('danger', 'Token inválido!');
        // A senha não pode ter mudado
        $this->assertFalse(Hash::check('senha123', $user->fresh()->password));
    }
}