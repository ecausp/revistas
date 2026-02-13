<?php

namespace Tests\Feature;

use App\Models\Empresa;
use App\Models\User;
use App\Models\Contato;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;
class EmpresaTest extends TestCase
{
    use RefreshDatabase; // Limpa o banco a cada teste para garantir dados frescos

    private $adminUser;
    private $financeiroUser;
    private $contatoUser;
    private $comumUser;


    protected function setUp(): void
    {
        parent::setUp();

        // 1. Limpa o cache de permissões do Spatie
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $guards = ['web', 'senhaunica'];

        foreach ($guards as $guard) {
            Permission::firstOrCreate(['name' => 'admin', 'guard_name' => $guard]);
            Permission::firstOrCreate(['name' => 'financeiro', 'guard_name' => $guard]);

            // CRIAR ROLES (Papéis)
            $roleAdmin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => $guard]);
            $roleFinan = Role::firstOrCreate(['name' => 'financeiro', 'guard_name' => $guard]);

            $roleAdmin->givePermissionTo('admin');
            $roleFinan->givePermissionTo('financeiro');
        }

        // 3. Setup do Admin (Atribui a ROLE)
        $this->adminUser = User::factory()->create();
        $this->adminUser->assignRole('admin'); 

        // 4. Setup do Financeiro (Atribui a ROLE)
        $this->financeiroUser = User::factory()->create();
        $this->financeiroUser->assignRole('financeiro');

        // 5. Setup do Usuário Comum (Sem nada)
        $this->comumUser = User::factory()->create();
    }

    /** @test */
    public function usuario_nao_logado_nao_pode_ver_empresas()
    {
        $response = $this->get(route('empresas.index'));
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function admin_pode_criar_empresa_com_dados_corretos()
    {
        // Define que vamos agir como Admin
        $response = $this->actingAs($this->adminUser)
            ->post(route('empresas.store'), [
                'razao_social' => 'Empresa Teste LTDA',
                'cnpj' => '12345678000199',
            ]);

        // Verifica se redirecionou para o index
        $response->assertRedirect(route('empresas.index'));
        $response->assertSessionHas('success');

        // Verifica se gravou no banco
        $this->assertDatabaseHas('empresas', [
            'razao_social' => 'Empresa Teste LTDA',
            'cnpj' => '12345678000199',
            'editor' => $this->adminUser->id // Verifica se salvou o editor certo
        ]);
    }

    /** @test */
    public function nao_pode_criar_empresa_sem_cnpj_ou_razao_social()
    {
        $response = $this->actingAs($this->adminUser)
            ->post(route('empresas.store'), [
                'razao_social' => '', // Vazio
                'cnpj' => '',
            ]);

        $response->assertSessionHasErrors(['razao_social', 'cnpj']);
        $this->assertDatabaseCount('empresas', 0);
    }

    /** @test */
    public function usuario_comum_nao_pode_criar_empresa()
    {
        // Tenta acessar o formulário
        $response = $this->actingAs($this->comumUser)
            ->get(route('empresas.create'));
        $response->assertForbidden(); // Espera erro 403

        // Tenta enviar o post
        $response = $this->actingAs($this->comumUser)
            ->post(route('empresas.store'), [
                'razao_social' => 'Hacker Corp',
                'cnpj' => '000',
            ]);
        $response->assertForbidden(); // Espera erro 403
    }

    /** @test */
    public function admin_ve_todas_as_empresas_no_index()
    {
        // Cria 3 empresas
        $empresas = Empresa::factory()->count(3)->create();

        $response = $this->actingAs($this->adminUser)
            ->get(route('empresas.index'));

        $response->assertOk(); // Status 200
        
        // Verifica se o nome das 3 empresas aparece na tela
        foreach ($empresas as $empresa) {
            $response->assertSee($empresa->razao_social);
        }
    }

    /** @test */
    public function contato_ve_apenas_sua_propria_empresa_no_index()
    {
        // Cria empresa A e B
        $empresaDoContato = Empresa::factory()->create(['razao_social' => 'Minha Empresa']);
        $outraEmpresa = Empresa::factory()->create(['razao_social' => 'Empresa Alheia']);

        // Cria usuário Contato e vincula à empresa A
        $contatoUser = User::factory()->create();
        Contato::create([
            'user_id' => $contatoUser->id,
            'empresa_id' => $empresaDoContato->id,
            'nome' => 'Fulano',
            'email' => 'fulano@teste.com',
            'telefone' => '1199999999'
        ]);

        $response = $this->actingAs($contatoUser)
            ->get(route('empresas.index'));

        $response->assertOk();
        $response->assertSee('Minha Empresa');
        $response->assertDontSee('Empresa Alheia'); // Garante que não vê a outra
    }

    /** @test */
    public function contato_nao_pode_acessar_detalhes_de_outra_empresa()
    {
        $empresaDoContato = Empresa::factory()->create();
        $outraEmpresa = Empresa::factory()->create();

        $contatoUser = User::factory()->create();
        Contato::create([
            'user_id' => $contatoUser->id,
            'empresa_id' => $empresaDoContato->id,
             'nome' => 'Ciclano', 'email' => 'c@c.com', 'telefone' => '123'
        ]);

        // Tenta acessar a rota show da OUTRA empresa
        $response = $this->actingAs($contatoUser)
            ->get(route('empresas.show', $outraEmpresa));

        // redireciona para home com erro
        $response->assertRedirect(route('home'));
        $response->assertSessionHas('error', 'Usuário não está associado a essa empresa!');
    }

    /** @test */
    public function admin_pode_editar_empresa()
    {
        $empresa = Empresa::factory()->create();

        $response = $this->actingAs($this->adminUser)
            ->put(route('empresas.update', $empresa), [
                'razao_social' => 'Nome Editado',
                'cnpj' => $empresa->cnpj,
            ]);

        $response->assertRedirect(route('empresas.index'));
        
        $this->assertDatabaseHas('empresas', [
            'id' => $empresa->id,
            'razao_social' => 'Nome Editado'
        ]);
    }

    /** @test */
    public function admin_pode_deletar_empresa()
    {
        $empresa = Empresa::factory()->create();

        $response = $this->actingAs($this->adminUser)
            ->delete(route('empresas.destroy', $empresa));

        $response->assertRedirect(route('empresas.index'));
        $this->assertDatabaseMissing('empresas', ['id' => $empresa->id]);
    }
}