<?php

namespace Tests\Feature;
use Tests\TestCase;
use App\Models\User;
use App\Models\Limite;
use App\Models\Licitacao;
use App\Models\Revista;
use App\Models\Servico;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LimiteTest extends TestCase
{
    use RefreshDatabase;

    private $financeiro;
    private $licitacao;
    private $revista;
    private $servico;

    protected function setUp(): void
    {
        parent::setUp();
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Setup de Guards
        $guards = ['web', 'senhaunica'];
        foreach ($guards as $guard) {
            // Criar AMBAS as permissões que o sistema consulta
            Permission::firstOrCreate(['name' => 'admin-financeiro', 'guard_name' => $guard]);
            Permission::firstOrCreate(['name' => 'admin', 'guard_name' => $guard]);

            $roleFinan = Role::firstOrCreate(['name' => 'financeiro', 'guard_name' => $guard]);
            $roleFinan->givePermissionTo('admin-financeiro');
        }
        $this->financeiro = User::factory()->create();
        $this->financeiro->assignRole('financeiro');

        // Dependências básicas para os testes
        $this->licitacao = Licitacao::factory()->create();
        $this->revista = Revista::factory()->create();
        $this->servico = Servico::factory()->create();
    }

    /** @test */
    public function financeiro_pode_criar_limite_para_um_servico()
    {
        $dados = [
            'licitacao_id' => $this->licitacao->id,
            'revista_id' => $this->revista->id,
            'servico_id' => $this->servico->id,
            'quantidade' => 10,
            'valor_unitario' => 150.00,
        ];

        $response = $this->actingAs($this->financeiro)
            ->post(route('limites.store'), $dados);

        $response->assertRedirect(route('limites.index'));
        
        // Verifica se o limite foi criado e se o EDITOR foi salvo
        $this->assertDatabaseHas('limites', [
            'licitacao_id' => $this->licitacao->id,
            'revista_id' => $this->revista->id,
            'servico_id' => $this->servico->id,
            'editor' => $this->financeiro->id 
        ]);
    }

    /** @test */
    public function total_por_revista_e_licitacao_retorna_soma_correta_via_json()
    {
        // Limite 1: 5 x 100 = 500
        Limite::factory()->create([
            'licitacao_id' => $this->licitacao->id,
            'revista_id' => $this->revista->id,
            'quantidade' => 5,
            'valor_unitario' => 100
        ]);

        // Limite 2: 2 x 250 = 500
        Limite::factory()->create([
            'licitacao_id' => $this->licitacao->id,
            'revista_id' => $this->revista->id,
            'quantidade' => 2,
            'valor_unitario' => 250
        ]);

        $response = $this->actingAs($this->financeiro)
            ->getJson(route('limites.total', [
                'licitacao_id' => $this->licitacao->id,
                'revista_id' => $this->revista->id
            ]));

        $response->assertOk();
        
        // A soma deve ser exata: 1000.00
        $response->assertJson([
            'total_limites' => 1000,
        ]);
    }

    /** @test */
    public function usuario_comum_nao_pode_criar_limite()
    {
        $userComum = User::factory()->create();

        $response = $this->actingAs($userComum)
            ->post(route('limites.store'), []);

        $response->assertForbidden();
    }

    /** @test */
    public function destroy_remove_limite_corretamente()
    {
        $limite = Limite::factory()->create();

        $response = $this->actingAs($this->financeiro)
            ->delete(route('limites.destroy', $limite));

        $response->assertRedirect(route('limites.index'));
        $this->assertDatabaseMissing('limites', ['id' => $limite->id]);
    }
}