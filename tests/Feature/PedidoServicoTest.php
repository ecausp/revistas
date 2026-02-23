<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\{User, Pedido, Solicitante, Revista, Licitacao, Servico, Empresa, Contato, PedidoServico};
use App\States\EmExecucao;
use App\States\EmElaboracao;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\{Role, Permission};

class PedidoServicoTest extends TestCase
{
    use RefreshDatabase;

    private $admin, $solicitanteUser, $contatoUser, $pedido, $licitacao, $servico, $empresa;

    protected function setUp(): void
    {
        parent::setUp();
        
        // 1. MOCK DO REPLICADO - Silencioso para evitar erros de layout
        $mockReplicado = \Mockery::mock('alias:Uspdev\Replicado\Pessoa');
        $mockReplicado->shouldIgnoreMissing();
        $mockReplicado->shouldReceive('dump')->andReturn(['nompesttd' => 'Editor de Teste']);

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 2. SETUP DE ROLES E GUARDS - Padrão EmpresaTest
        $guards = ['web', 'senhaunica'];
        foreach ($guards as $guard) {
            Permission::firstOrCreate(['name' => 'admin', 'guard_name' => $guard]);
            $roleAdmin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => $guard]);
            $roleAdmin->givePermissionTo('admin');
        }

        // 3. CRIAÇÃO DA AMBIÊNCIA
        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

        $this->licitacao = Licitacao::factory()->create();
        $this->revista = Revista::factory()->create();
        
        $this->solicitanteUser = User::factory()->create();
        $solicitante = Solicitante::create([
            'user_id' => $this->solicitanteUser->id, 
            'revista_id' => $this->revista->id
        ]);

        $this->empresa = Empresa::factory()->create(['razao_social' => 'Empresa Alfa']);
        $this->contatoUser = User::factory()->create();
        Contato::create([
            'user_id' => $this->contatoUser->id, 
            'empresa_id' => $this->empresa->id
        ]);

        $this->servico = Servico::factory()->create(['descricao' => 'Serviço de Teste']);

        // 4. POPULAÇÃO DA TABELA TRIPLA (via SQL para bater com o Controller)
        DB::table('empresa_licitacao_servico')->insert([
            'empresa_id'   => $this->empresa->id,
            'licitacao_id' => $this->licitacao->id,
            'servico_id'   => $this->servico->id,
            'valor_unitario' => 100.00,
            'prazo'        => 10,
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);

        // 5. PEDIDO EM EXECUÇÃO (Para aparecer no index do Controller)
        $this->pedido = Pedido::factory()->create([
            'status' => EmExecucao::class,
            'revista_id' => $this->revista->id,
            'licitacao_id' => $this->licitacao->id,
            'solicitante_id' => $solicitante->id
        ]);

        // Vincula o serviço ao pedido
        PedidoServico::factory()->emExecucao()->create([
            'pedido_id' => $this->pedido->id,
            'servico_id' => $this->servico->id,
        ]);
    }

    /** @test */
    public function admin_visualiza_todos_os_servicos_em_execucao()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('servicos-execucao.index'));

        $response->assertStatus(200);
        $response->assertSee('Empresa Alfa');
        $response->assertSee('Serviço de Teste');
    }

    /** @test */
    public function contato_visualiza_apenas_servicos_vinculados_a_sua_empresa()
    {
        // Cria outro serviço de outra empresa para garantir o isolamento
        $outraEmpresa = Empresa::factory()->create(['razao_social' => 'Empresa Beta']);
        $outroServico = Servico::factory()->create(['descricao' => 'Serviço Alheio']);
        
        DB::table('empresa_licitacao_servico')->insert([
            'empresa_id' => $outraEmpresa->id,
            'licitacao_id' => $this->licitacao->id,
            'servico_id' => $outroServico->id,
            'valor_unitario' => 50.00, 'prazo' => 1
        ]);

        $outroPedido = Pedido::factory()->create(['status' => EmExecucao::class, 'licitacao_id' => $this->licitacao->id]);
        PedidoServico::factory()->emExecucao()->create(['pedido_id' => $outroPedido->id, 'servico_id' => $outroServico->id]);

        $response = $this->actingAs($this->contatoUser)
            ->get(route('servicos-execucao.index'));

        $response->assertStatus(200);
        $response->assertSee('Empresa Alfa');
        $response->assertDontSee('Empresa Beta');
        $response->assertDontSee('Serviço Alheio');
    }

    /** @test */
    public function solicitante_visualiza_apenas_servicos_de_sua_revista()
    {
        $outraRevista = Revista::factory()->create(['name' => 'Revista Alheia']);
        $outroPedido = Pedido::factory()->create([
            'status' => EmExecucao::class, 
            'revista_id' => $outraRevista->id,
            'licitacao_id' => $this->licitacao->id
        ]);
        PedidoServico::factory()->emExecucao()->create(['pedido_id' => $outroPedido->id, 'servico_id' => $this->servico->id]);

        $response = $this->actingAs($this->solicitanteUser)
            ->get(route('servicos-execucao.index'));

        $response->assertStatus(200);
        // Deve ver apenas o pedido da sua revista (ID do pedido original)
        $response->assertSee((string) $this->pedido->id);
        $this->assertCount(1, $response->viewData('listaUnificada'));
    }

    /** @test */
    public function servicos_com_status_diferente_de_execucao_nao_sao_listados()
    {
        $pedidoElaboracao = Pedido::factory()->create(['status' => EmElaboracao::class]);
        PedidoServico::factory()->create(['pedido_id' => $pedidoElaboracao->id, 'servico_id' => $this->servico->id]);

        $response = $this->actingAs($this->admin)
            ->get(route('servicos-execucao.index'));
        
            $this->assertCount(1, $response->viewData('listaUnificada'));
    }
}