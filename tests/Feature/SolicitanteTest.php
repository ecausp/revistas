<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Revista;
use App\Models\Solicitante;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Http\Controllers\SolicitanteController;

class SolicitanteTest extends TestCase
{
    use RefreshDatabase;

    private $admin;
    private $revista;

    protected function setUp(): void
    {
        parent::setUp();
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Valor da hierarquia para bater com o Model User
        $guard = 'senhaunica'; // Se mudar no Model, mudar aqui!

        Permission::firstOrCreate(['name' => 'admin', 'guard_name' => $guard]);
        Permission::firstOrCreate(['name' => 'financeiro', 'guard_name' => $guard]);
        Permission::firstOrCreate(['name' => 'user', 'guard_name' => $guard]);
        Permission::firstOrCreate(['name' => 'revistas.revista', 'guard_name' => $guard]);

        $roleAdmin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => $guard]);
        $roleAdmin->givePermissionTo('admin');

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

        $this->revista = Revista::factory()->create();
    }

    /** @test */
    public function admin_acessa_criacao_de_solicitante()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('solicitantes.create', $this->revista));

        $response->assertOk();
        $response->assertViewIs('solicitantes.create');
        $response->assertViewHas('revista');
    }

    /** @test */
    public function usuario_comum_nao_autorizado_eh_redirecionado()
    {
        $userComum = User::factory()->create();

        $response = $this->actingAs($userComum)
            ->get(route('solicitantes.create', $this->revista));

        $response->assertRedirect(route('home'));
        $response->assertSessionHas('error');
    }

    /** @test */
    public function store_cria_novo_usuario_e_vincula_como_solicitante()
    {
        $dados = [
            'name' => 'Novo Solicitante',
            'email' => 'novo@usp.br',
            'codpes' => '1234567',
            'revista_id' => $this->revista->id,
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('solicitantes.store'), $dados);

        $response->assertRedirect(route('revistas.index'));
        $response->assertSessionHas('success');

        // Verifica se usuário foi criado
        $this->assertDatabaseHas('users', [
            'email' => 'novo@usp.br',
            'codpes' => '1234567'
        ]);

        $novoUser = User::where('email', 'novo@usp.br')->first();

        // Verifica permissões
        $this->assertTrue($novoUser->hasPermissionTo('user'));
        $this->assertTrue($novoUser->hasPermissionTo('revistas.revista'));

        // Verifica vínculo de solicitante
        $this->assertDatabaseHas('solicitantes', [
            'user_id' => $novoUser->id,
            'revista_id' => $this->revista->id
        ]);
    }

    /** @test */
    public function buscar_usuario_via_ajax_retorna_json_correto()
    {
        // Mockamos o método do controller que chama o ReplicadoTemp
        $this->partialMock(SolicitanteController::class, function ($mock) {
            $mock->shouldAllowMockingProtectedMethods();
            $mock->shouldReceive('listarServidoresDocentes')
                ->once()
                ->andReturn([
                    [
                        'codpes' => '999',
                        'nompesttd' => 'Fulano de Tal',
                        'tipvin' => 'Servidor',
                        'codema' => 'fulano@usp.br',
                        'numseqpes' => 1
                    ],
                    [
                        'codpes' => '888',
                        'nompesttd' => 'Ciclano',
                        'tipvin' => 'Docente',
                        'codema' => 'ciclano@usp.br',
                        'numseqpes' => 2
                    ]
                ]);
        });

        // Chamada AJAX filtrando por "Fulano"
        $response = $this->actingAs($this->admin)
            ->getJson(route('solicitantes.buscarUsuario', [
                'nome' => 'Fulano',
                'revista_id' => $this->revista->id
            ]));

        $response->assertOk();
        
        // Verifica se o JSON contém apenas o Fulano e a estrutura esperada
        $response->assertJsonCount(1);
        $response->assertJsonFragment([
            'name' => 'Fulano de Tal',
            'codpes' => '999',
            'ja_cadastrado' => false
        ]);
    }
}
