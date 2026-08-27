<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Licitacao;
use App\Models\Empresa;
use App\Models\Revista;
use App\Models\Solicitante;
use App\Models\Saldo;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LicitacaoTest extends TestCase
{
    use RefreshDatabase;

    private $admin;
    private $financeiro;
    private $userComum;

    protected function setUp(): void
    {
        parent::setUp();
        
        // 1. Limpa o cache do Spatie para não ter resquícios de testes anteriores
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $guards = ['web', 'senhaunica']; 

        foreach ($guards as $guard) {
            // Cria as permissões para cada guard
            Permission::firstOrCreate(['name' => 'admin-financeiro', 'guard_name' => $guard]);
            Permission::firstOrCreate(['name' => 'admin', 'guard_name' => $guard]);
            Permission::firstOrCreate(['name' => 'financeiro', 'guard_name' => $guard]);

            // Cria as Roles para cada guard
            $roleAdmin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => $guard]);
            $roleFinan = Role::firstOrCreate(['name' => 'financeiro', 'guard_name' => $guard]);

            // Garante que as Roles tenham as permissões necessárias
            $roleAdmin->givePermissionTo('admin');
            $roleFinan->givePermissionTo('financeiro');
            $roleFinan->givePermissionTo('admin-financeiro');
        }

        // 3. Criação dos usuários de teste
        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

        $this->financeiro = User::factory()->create();
        $this->financeiro->assignRole('financeiro');

        $this->userComum = User::factory()->create();
    }

    /** @test */
    public function apenas_usuarios_com_permissao_podem_acessar_create_de_licitacao()
    {
        // Financeiro acessa
        $this->actingAs($this->financeiro)
            ->get(route('licitacoes.create'))
            ->assertOk()
            ->assertSee('name="ano"', false)
            ->assertSee('type="text"', false)
            ->assertDontSee('type="month"', false);

        // Usuário comum é barrado pela Policy/Authorize
        $this->actingAs($this->userComum)->get(route('licitacoes.create'))->assertForbidden();
    }

    /** @test */
    public function store_converte_valor_brasileiro_para_float_corretamente()
    {
        $dados = [
            'descricao' => 'Licitação de Teste Moeda',
            'ano' => '2024',
            'valor_total' => '1.500,50', // Formato BR
        ];

        $response = $this->actingAs($this->financeiro)->post(route('licitacoes.store'), $dados);

        $response->assertRedirect(route('licitacoes.index'));
        
        // Verifica se no banco o valor virou float
        $this->assertDatabaseHas('licitacoes', [
            'descricao' => 'Licitação de Teste Moeda',
            'valor_total' => 1500.50
        ]);
    }

    /** @test */
    public function solicitante_so_ve_saldos_da_sua_propria_revista_no_show()
    {
        $revistaA = Revista::factory()->create();
        $revistaB = Revista::factory()->create();
        
        $licitacao = Licitacao::factory()->create();
        
        // Saldos de revistas diferentes
        $saldoA = Saldo::factory()->create(['revista_id' => $revistaA->id, 'licitacao_id' => $licitacao->id]);
        $saldoB = Saldo::factory()->create(['revista_id' => $revistaB->id, 'licitacao_id' => $licitacao->id]);

        // Usuário vinculado apenas à Revista A
        $solicitante = User::factory()->create();
        Solicitante::create(['user_id' => $solicitante->id, 'revista_id' => $revistaA->id]);

        $response = $this->actingAs($solicitante)->get(route('licitacoes.show', $licitacao));

        $response->assertOk();
        $saldosNaView = $response->viewData('saldos');

        // Garante que ele vê o saldo da revista A, mas não da B
        $this->assertTrue($saldosNaView->contains($saldoA));
        $this->assertFalse($saldosNaView->contains($saldoB));
    }

    /** @test */
    public function financeiro_ve_todos_os_saldos_no_show()
    {
        $licitacao = Licitacao::factory()->create();
        Saldo::factory()->count(3)->create(['licitacao_id' => $licitacao->id]);

        $response = $this->actingAs($this->financeiro)->get(route('licitacoes.show', $licitacao));

        $this->assertCount(3, $response->viewData('saldos'));
    }

    /** @test */
    public function pode_vincular_empresas_a_uma_licitacao()
    {
        $licitacao = Licitacao::factory()->create();
        $empresas = Empresa::factory()->count(2)->create();
        $ids = $empresas->pluck('id')->toArray();

        $response = $this->actingAs($this->financeiro)
            ->post(route('licitacoes.addEmpresas', $licitacao), [
                'empresas' => [
                    $ids[0] => ['selecionada' => 1, 'classificacao' => 1],
                    $ids[1] => ['selecionada' => 1, 'classificacao' => 2],
                ],
                'empresa_vigente' => $ids[0],
            ]);

        $response->assertRedirect(route('licitacoes.index'));
        
        // Verifica se o relacionamento foi criado na tabela pivô
        foreach ($ids as $indice => $id) {
            $this->assertDatabaseHas('empresa_licitacao', [
                'licitacao_id' => $licitacao->id,
                'empresa_id' => $id,
                'classificacao' => $indice + 1,
                'vigente' => $indice === 0,
            ]);
        }
    }

    /** @test */
    public function pode_substituir_empresa_vigente_com_motivo()
    {
        $licitacao = Licitacao::factory()->create();
        [$vencedora, $segundaColocada] = Empresa::factory()->count(2)->create();

        $licitacao->empresas()->attach([
            $vencedora->id => ['classificacao' => 1, 'vigente' => true],
            $segundaColocada->id => ['classificacao' => 2, 'vigente' => false],
        ]);

        $response = $this->actingAs($this->financeiro)
            ->post(route('licitacoes.addEmpresas', $licitacao), [
                'empresas' => [
                    $vencedora->id => ['selecionada' => 1, 'classificacao' => 1],
                    $segundaColocada->id => [
                        'selecionada' => 1,
                        'classificacao' => 2,
                        'motivo_substituicao' => 'Empresa vencedora desistiu do contrato.',
                    ],
                ],
                'empresa_vigente' => $segundaColocada->id,
            ]);

        $response->assertRedirect(route('licitacoes.index'));
        $this->assertDatabaseHas('empresa_licitacao', [
            'licitacao_id' => $licitacao->id,
            'empresa_id' => $vencedora->id,
            'vigente' => false,
        ]);
        $this->assertDatabaseHas('empresa_licitacao', [
            'licitacao_id' => $licitacao->id,
            'empresa_id' => $segundaColocada->id,
            'classificacao' => 2,
            'vigente' => true,
            'motivo_substituicao' => 'Empresa vencedora desistiu do contrato.',
        ]);
    }

    /** @test */
    public function exige_motivo_quando_a_empresa_vigente_nao_e_a_primeira_colocada()
    {
        $licitacao = Licitacao::factory()->create();
        [$vencedora, $segundaColocada] = Empresa::factory()->count(2)->create();

        $response = $this->actingAs($this->financeiro)
            ->from(route('licitacoes.index'))
            ->post(route('licitacoes.addEmpresas', $licitacao), [
                'empresas' => [
                    $vencedora->id => ['selecionada' => 1, 'classificacao' => 1],
                    $segundaColocada->id => ['selecionada' => 1, 'classificacao' => 2],
                ],
                'empresa_vigente' => $segundaColocada->id,
            ]);

        $response->assertRedirect(route('licitacoes.index'));
        $response->assertSessionHasErrors([
            "empresas.{$segundaColocada->id}.motivo_substituicao",
        ]);
        $this->assertDatabaseCount('empresa_licitacao', 0);
    }

    /** @test */
    public function exibe_classificacao_vigencia_e_motivo_da_substituicao()
    {
        $licitacao = Licitacao::factory()->create();
        $empresa = Empresa::factory()->create(['razao_social' => 'Empresa Classificada']);
        $licitacao->empresas()->attach($empresa->id, [
            'classificacao' => 2,
            'vigente' => true,
            'motivo_substituicao' => 'Substituição por desistência.',
        ]);

        $index = $this->actingAs($this->financeiro)->get(route('licitacoes.index'));

        $index->assertOk();
        $index->assertSee('2ª - Empresa Classificada');
        $index->assertSee('(Vigente)');

        $licitacao->load('empresas');
        $this->view('licitacoes.partials.empresas', ['licitacao' => $licitacao])
            ->assertSee('2ª')
            ->assertSee('Vigente')
            ->assertSee('Substituição por desistência.');

        $empresa->load('licitacoes');
        $this->view('empresas.partials.licitacoes', ['empresa' => $empresa])
            ->assertSee('2ª')
            ->assertSee('Vigente')
            ->assertSee('Substituição por desistência.');
    }

    /** @test */
    public function admin_pode_deletar_licitacao()
    {
        $licitacao = Licitacao::factory()->create();

        $response = $this->actingAs($this->admin)->delete(route('licitacoes.destroy', $licitacao));

        $response->assertRedirect(route('licitacoes.index'));
        $this->assertDatabaseMissing('licitacoes', ['id' => $licitacao->id]);
    }
}
