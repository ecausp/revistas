<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Revista;
use App\Models\Solicitante;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Http\Controllers\RevistaController;

class RevistaTest extends TestCase
{
    use RefreshDatabase;

    private $adminUser;
    private $comumUser;

    protected function setUp(): void
    {
        parent::setUp();
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $guards = ['web', 'senhaunica'];
        foreach ($guards as $guard) {
            Permission::firstOrCreate(['name' => 'admin', 'guard_name' => $guard]);
            Permission::firstOrCreate(['name' => 'financeiro', 'guard_name' => $guard]);
            Permission::firstOrCreate(['name' => 'user', 'guard_name' => $guard]); 
            Permission::firstOrCreate(['name' => 'revistas.revista', 'guard_name' => $guard]);
            
            $roleAdmin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => $guard]);
            $roleFinan = Role::firstOrCreate(['name' => 'financeiro', 'guard_name' => $guard]);
            $roleAdmin->givePermissionTo('admin');
            $roleFinan->givePermissionTo('financeiro');
        }

        $this->adminUser = User::factory()->create();
        $this->adminUser->assignRole('admin');
        $this->comumUser = User::factory()->create();
        \Mockery::close();
    }

    /**
     * Helper para mockar o retorno padrão do Replicado Pessoa
     */
private function mockPessoaPadrão()
    {
        $this->partialMock(\Uspdev\Replicado\Pessoa::class, function ($mock) {
            $mock->shouldReceive('dump')
                ->andReturn([
                    'nompesttd' => 'Nome de Teste',
                    'nompes'    => 'Nome de Teste',
                    'codpes'    => 123456,
                    'etrhie'    => 'Docente',
                    'codundclg' => 8,
                    'email'     => 'teste@usp.br'
                ]);
        });
    }

    /** @test */
    public function admin_acessa_index_e_ve_todas_as_revistas()
    {
        Revista::factory()->count(3)->create(['centro_despesa' => 'CD-NAO-EXIBIR']);

        $this->mockPessoaPadrão();

        $response = $this->actingAs($this->adminUser)->get(route('revistas.index'));

        $response->assertOk();
        $response->assertViewHas('revistas');
        $response->assertDontSee('CD-NAO-EXIBIR');
        $this->assertCount(3, $response->viewData('revistas'));
    }

    /** @test */
    public function centro_de_despesa_nao_aparece_na_serializacao_da_revista()
    {
        $revista = Revista::factory()->create(['centro_despesa' => 'CD-NAO-EXIBIR']);

        $this->assertArrayNotHasKey('centro_despesa', $revista->toArray());
        $this->assertSame('CD-NAO-EXIBIR', $revista->centro_despesa);
    }



    /** @test */
    public function admin_pode_criar_revista_e_sistema_gera_solicitantes()
    {
        $codpes = 987654;
        $usuario = User::factory()->create(['codpes' => $codpes]);

        $dados = [
            'name' => 'Revista Nova',
            'responsavel_centro_despesa' => [$codpes],
            'email_revista' => 'nova@usp.br',
        ];

        $response = $this->actingAs($this->adminUser)->post(route('revistas.store'), $dados);

        $response->assertRedirect(route('revistas.index'));
        $this->assertDatabaseHas('revistas', [
            'name' => 'Revista Nova',
            'centro_despesa' => null,
        ]);
        $this->assertDatabaseHas('solicitantes', ['user_id' => $usuario->id]);
    }

    /** @test */
    public function admin_acessa_pagina_de_edicao_com_dados_mockados()
    {
        $revista = Revista::factory()->create();

        $this->partialMock(RevistaController::class, function ($mock) {
            $mock->shouldAllowMockingProtectedMethods();
            $mock->shouldReceive('listarServidoresDocentes')->andReturn([]);
        });

        $this->mockPessoaPadrão();

        $response = $this->actingAs($this->adminUser)->get(route('revistas.edit', $revista));

        $response->assertOk();
        $response->assertDontSee('Centro de Despesa');
    }

    /** @test */
    public function admin_pode_atualizar_revista_e_trocar_responsaveis()
    {
        $userA = User::factory()->create(['codpes' => 111, 'name' => 'User A']);
        $revista = Revista::factory()->create(['responsavel_centro_despesa' => '111']);
        $centroDespesaOriginal = (string) $revista->centro_despesa;
        Solicitante::create(['user_id' => $userA->id, 'revista_id' => $revista->id]);
        $userA->givePermissionTo('revistas.revista');

        $userB = User::factory()->create(['codpes' => 222, 'name' => 'User B']);

        $dadosUpdate = [
            'name' => 'Revista Nome Alterado',
            'responsavel_centro_despesa' => ['222'],
            'email_revista' => 'novoemail@usp.br',
        ];

        $response = $this->actingAs($this->adminUser)
            ->patch(route('revistas.update', $revista), $dadosUpdate);

        $response->assertRedirect(route('revistas.index'));

        $this->assertDatabaseHas('revistas', ['id' => $revista->id, 'responsavel_centro_despesa' => '222']);
        $this->assertSame($centroDespesaOriginal, $revista->fresh()->centro_despesa);
        $this->assertDatabaseHas('solicitantes', ['user_id' => $userB->id]);
        $this->assertDatabaseMissing('solicitantes', ['user_id' => $userA->id]);
        $this->assertFalse($userA->fresh()->hasPermissionTo('revistas.revista'));
    }

    /** @test */
    public function admin_pode_deletar_revista()
    {
        $revista = Revista::factory()->create();

        $response = $this->actingAs($this->adminUser)
            ->delete(route('revistas.destroy', $revista));

        $response->assertRedirect(route('revistas.index'));
        $this->assertDatabaseMissing('revistas', ['id' => $revista->id]);
    }
}
