<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\{User, Pedido, Solicitante, Revista, Licitacao, Servico, Empresa, Contato, EmpresaLicitacaoServico, Chat};
use App\States\{EmElaboracao, EmAnaliseEmpresa};
use App\Mail\ChatEnviadoMail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class ChatTest extends TestCase
{
    use RefreshDatabase;

    private $solicitanteUser, $contatoUser, $admin, $pedido, $licitacao, $servico, $empresa;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
        
        // 1. MOCK DO REPLICADO - Corrigido com shouldIgnoreMissing()
        // Isso impede que o teste exploda ao encontrar métodos como obterRamalUsp()
        $mockReplicado = \Mockery::mock('alias:Uspdev\Replicado\Pessoa');
        $mockReplicado->shouldIgnoreMissing(); 
        $mockReplicado->shouldReceive('dump')
            ->andReturn(['nompesttd' => 'Editor de Teste']);

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 2. Setup de Roles e Permissions (O padrão que funciona no seu projeto)
        $guards = ['web', 'senhaunica'];
        foreach ($guards as $guard) {
            Permission::firstOrCreate(['name' => 'admin', 'guard_name' => $guard]);
            Permission::firstOrCreate(['name' => 'financeiro', 'guard_name' => $guard]);

            $roleAdmin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => $guard]);
            $roleFinan = Role::firstOrCreate(['name' => 'financeiro', 'guard_name' => $guard]);

            $roleAdmin->givePermissionTo('admin');
            $roleFinan->givePermissionTo('financeiro');
        }

        // 3. Setup dos Usuários e Ambiência
        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin'); 

        $this->licitacao = Licitacao::factory()->create();
        $this->revista = Revista::factory()->create();
        
        $this->solicitanteUser = User::factory()->create();
        $solicitante = Solicitante::create([
            'user_id' => $this->solicitanteUser->id, 
            'revista_id' => $this->revista->id
        ]);

        $this->empresa = Empresa::factory()->create();
        $this->contatoUser = User::factory()->create();
        Contato::create([
            'user_id' => $this->contatoUser->id, 
            'empresa_id' => $this->empresa->id
        ]);

        $this->servico = Servico::factory()->create();

        // 4. Integridade da Tabela Tripla para o Controller
        \DB::table('empresa_licitacao_servico')->insert([
            'empresa_id' => $this->empresa->id,
            'servico_id' => $this->servico->id,
            'licitacao_id' => $this->licitacao->id,
            'valor_unitario' => 10.00,
            'prazo' => 5,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $this->pedido = Pedido::factory()->create([
            'status' => EmAnaliseEmpresa::class,
            'revista_id' => $this->revista->id,
            'licitacao_id' => $this->licitacao->id,
            'solicitante_id' => $solicitante->id
        ]);
        
        $this->pedido->servicos()->attach($this->servico->id, ['quantidade' => 1, 'valor_unitario' => 10]);
    }

    /** @test */
    public function trava_chat_no_status_em_elaboracao()
    {
        $this->pedido->update(['status' => EmElaboracao::class]);

        $this->actingAs($this->solicitanteUser)
            ->post(route('chat.store', $this->pedido), ['message' => 'Oi'])
            ->assertRedirect(route('home'))
            ->assertSessionHas('danger');
    }

    /** @test */
    public function valida_autoria_da_mensagem_pelo_usuario_logado()
    {
        $this->actingAs($this->solicitanteUser)
            ->post(route('chat.store', $this->pedido), ['message' => 'Teste']);

        $this->assertDatabaseHas('chats', [
            'pedido_id' => $this->pedido->id,
            'user_id' => $this->solicitanteUser->id
        ]);
    }

    /** @test */
    public function notifica_participantes_corretamente_via_tabela_tripla()
    {
        $this->actingAs($this->solicitanteUser)
            ->post(route('chat.store', $this->pedido), ['message' => 'Notificar']);

        Mail::assertSent(ChatEnviadoMail::class);
    }

    /** @test */
    public function view_do_pedido_contem_o_chat_e_identifica_contato_corretamente()
    {
        Chat::create([
            'pedido_id' => $this->pedido->id,
            'user_id' => $this->contatoUser->id,
            'message' => 'Msg da Empresa'
        ]);

        $response = $this->actingAs($this->solicitanteUser)
            ->get(route('pedidos.show', $this->pedido));

        $response->assertOk();
        $response->assertSee('- Contato da Empresa');
    }

    /** @test */
    public function admin_pode_ver_pedido_e_chat_mesmo_nao_sendo_participante()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('pedidos.show', $this->pedido));

        $response->assertOk();
    }

    /** @test */
    public function nega_acesso_ao_pedido_para_usuario_intruso()
    {
        $intruso = User::factory()->create();
        $this->actingAs($intruso)
            ->get(route('pedidos.show', $this->pedido))
            ->assertRedirect(route('home'));
    }
}