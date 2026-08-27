<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Revista;
use App\Models\Licitacao;
use App\Models\Saldo;
use App\Models\Solicitante;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SaldoTest extends TestCase
{
    use RefreshDatabase;

    private $financeiro;
    private $licitacao;
    private $revista;

    protected function setUp(): void
    {
        parent::setUp();
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Setup de Guards
        $guards = ['web', 'senhaunica'];
        foreach ($guards as $guard) {
            Permission::firstOrCreate(['name' => 'financeiro', 'guard_name' => $guard]);
            Permission::firstOrCreate(['name' => 'admin', 'guard_name' => $guard]);
            $roleFinan = Role::firstOrCreate(['name' => 'financeiro', 'guard_name' => $guard]);
            $roleFinan->givePermissionTo('financeiro');
        }

        $this->financeiro = User::factory()->create();
        $this->financeiro->assignRole('financeiro');

        // Cria uma licitação com 5000 de total e 0 já cadastrado
        $this->licitacao = Licitacao::factory()->create([
            'valor_total' => 5000.00,
            'valor_cadastrado' => 0
        ]);

        $this->revista = Revista::factory()->create();
    }

    /** @test */
    public function store_salva_saldo_dentro_do_limite_da_licitacao()
    {
        $dados = [
            'licitacao_id' => $this->licitacao->id,
            'revista_id' => $this->revista->id,
            'recurso_recebido' => 2000.00,
            'recurso_estagio' => 500.00,
            'observacao' => 'Receita de inscrições da Pós-Graduação',
        ];

        $response = $this->actingAs($this->financeiro)
            ->post(route('saldos.store'), $dados);

        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('saldos', [
            'licitacao_id' => $this->licitacao->id,
            'revista_id' => $this->revista->id,
            'recurso_recebido' => 2000.00,
            'recurso_estagio' => 500.00,
            'observacao' => 'Receita de inscrições da Pós-Graduação',
        ]);

        // Verifica se atualizou o valor_cadastrado na licitação
        $this->assertEquals(2000.00, $this->licitacao->fresh()->valor_cadastrado);

        $saldo = Saldo::where('licitacao_id', $this->licitacao->id)
            ->where('revista_id', $this->revista->id)
            ->firstOrFail();
        $this->actingAs($this->financeiro)
            ->get(route('saldos.show', $saldo))
            ->assertOk()
            ->assertSee('Receita de inscrições da Pós-Graduação');
    }

    /** @test */
    public function store_assume_zero_quando_recurso_de_estagio_nao_e_informado()
    {
        $dados = [
            'licitacao_id' => $this->licitacao->id,
            'revista_id' => $this->revista->id,
            'recurso_recebido' => 2000.00,
            'recurso_estagio' => '',
            'observacao' => 'Outra fonte de recurso',
        ];

        $response = $this->actingAs($this->financeiro)
            ->post(route('saldos.store'), $dados);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('saldos', [
            'licitacao_id' => $this->licitacao->id,
            'revista_id' => $this->revista->id,
            'recurso_estagio' => 0,
            'observacao' => 'Outra fonte de recurso',
        ]);
    }

    /** @test */
    public function observacao_e_exibida_em_modal_nas_views_de_licitacao_e_revista()
    {
        $saldo = Saldo::factory()->create([
            'licitacao_id' => $this->licitacao->id,
            'revista_id' => $this->revista->id,
            'observacao' => 'Receita de processo seletivo',
        ]);
        $views = [
            'licitacoes.partials.saldos',
            'revistas.partials.saldos',
        ];

        foreach ($views as $view) {
            $response = $this->view($view, ['saldos' => collect([$saldo])]);

            $response->assertSee('data-target="#observacaoSaldoModal' . $saldo->id . '"', false);
            $response->assertSeeText('Receita de processo seletivo');
        }
    }

    /** @test */
    public function store_nao_permite_saldo_que_ultrapassa_valor_total_da_licitacao()
    {
        // Tenta cadastrar 6000 em uma licitação de 5000
        $dados = [
            'licitacao_id' => $this->licitacao->id,
            'revista_id' => $this->revista->id,
            'recurso_recebido' => 6000.00,
            'recurso_estagio' => 0,
        ];

        $response = $this->actingAs($this->financeiro)
            ->post(route('saldos.store'), $dados);

        $response->assertSessionHasErrors(['recurso_recebido']);
        $this->assertDatabaseMissing('saldos', ['recurso_recebido' => 6000.00]);
    }

    /** @test */
    public function solicitante_nao_pode_ver_saldo_de_outra_revista()
    {
        $outraRevista = Revista::factory()->create();
        $saldoOutra = Saldo::factory()->create([
            'revista_id' => $outraRevista->id,
            'licitacao_id' => $this->licitacao->id
        ]);

        // Usuário é solicitante da MINHA revista
        $userSolicitante = User::factory()->create();
        Solicitante::create([
            'user_id' => $userSolicitante->id, 
            'revista_id' => $this->revista->id
        ]);

        // Tenta acessar o show do saldo da OUTRA revista
        $response = $this->actingAs($userSolicitante)
            ->get(route('saldos.show', $saldoOutra));

        $response->assertRedirect(route('home'));
        $response->assertSessionHas('error', 'Usuário não autorizado a acessar esse recurso');
    }

    /** @test */
    public function get_valor_recebido_retorna_json_correto_para_ajax()
    {
        Saldo::factory()->create([
            'licitacao_id' => $this->licitacao->id,
            'revista_id' => $this->revista->id,
            'recurso_recebido' => 1234.56
        ]);

        $response = $this->actingAs($this->financeiro)
            ->getJson(route('saldos.valor-recebido', [
                'licitacao_id' => $this->licitacao->id,
                'revista_id' => $this->revista->id
            ]));

        $response->assertOk();
        $response->assertJson(['valor_recurso_financeiro' => 1234.56]);
    }
}
