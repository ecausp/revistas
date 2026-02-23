<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Empresa;
use App\Models\Contato;
use App\Mail\ContatoCadastradoMail;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ContatoTest extends TestCase
{
    use RefreshDatabase;

    private $adminUser;
    private $comumUser;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Limpa cache de permissões
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 2. Cria Roles e Permissions para 'web' e 'senhaunica'
        $guards = ['web', 'senhaunica'];

        foreach ($guards as $guard) {
            // CRIA AS PERMISSÕES (Isso corrige o erro!)
            Permission::firstOrCreate(['name' => 'admin', 'guard_name' => $guard]);
            Permission::firstOrCreate(['name' => 'financeiro', 'guard_name' => $guard]);
            
            // Permissões específicas do Contato
            Permission::firstOrCreate(['name' => 'user', 'guard_name' => $guard]);
            Permission::firstOrCreate(['name' => 'revistas.empresa', 'guard_name' => $guard]);

            // CRIA AS ROLES
            $roleAdmin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => $guard]);
            $roleFinan = Role::firstOrCreate(['name' => 'financeiro', 'guard_name' => $guard]);
            
            // (Opcional) Dá a permissão para a role
            $roleAdmin->givePermissionTo('admin');
            $roleFinan->givePermissionTo('financeiro');
        }

        // 3. Setup do Admin
        $this->adminUser = User::factory()->create();
        $this->adminUser->assignRole('admin'); 

        // 4. Setup do Usuário Comum
        $this->comumUser = User::factory()->create();
    }
    
    /** @test */
    public function usuario_comum_nao_acessa_pagina_de_criar_contato()
    {
        $empresa = Empresa::factory()->create();

        // Tenta acessar a rota GET
        $response = $this->actingAs($this->comumUser)
            ->get(route('contatos.create', $empresa->id));

        // Seu controller redireciona para a home com mensagem de erro
        $response->assertRedirect(route('home'));
        $response->assertSessionHas('error');
    }

    /** @test */
    public function admin_acessa_pagina_de_criar_contato()
    {
        $empresa = Empresa::factory()->create();

        $response = $this->actingAs($this->adminUser)
            ->get(route('contatos.create', $empresa->id));

        $response->assertOk(); // Status 200
        $response->assertViewIs('contatos.create');
        $response->assertViewHas('empresa');
    }

    /** @test */
    public function admin_pode_criar_contato_e_sistema_cria_usuario_automaticamente()
    {
        // 1. Fake no envio de E-mail (não envia de verdade)
        Mail::fake();

        $empresa = Empresa::factory()->create();

        // Dados do formulário
        $dadosContato = [
            'name' => 'Novo Contato Teste',
            'email' => 'novocontato@teste.usp.br', // E-mail novo
            'telefone' => '(11) 99999-8888',
            'empresa_id' => $empresa->id,
        ];

        // 2. Envia o POST
        $response = $this->actingAs($this->adminUser)
            ->post(route('contatos.store'), $dadosContato);

        // 3. Verificações de Redirecionamento
        $response->assertRedirect(route('empresas.index'));
        $response->assertSessionHas('success');

        // 4. Verifica se criou o USUÁRIO na tabela users
        $this->assertDatabaseHas('users', [
            'email' => 'novocontato@teste.usp.br',
            'name' => 'Novo Contato Teste',
            'telefone' => '(11) 99999-8888'
        ]);

        // Recupera o usuário criado para mais testes
        $novoUsuario = User::where('email', 'novocontato@teste.usp.br')->first();

        // 5. Verifica se criou o VÍNCULO na tabela contatos
        $this->assertDatabaseHas('contatos', [
            'user_id' => $novoUsuario->id,
            'empresa_id' => $empresa->id
        ]);

        // 6. Verifica se o sistema tentou enviar o e-mail para a pessoa certa
        Mail::assertSent(ContatoCadastradoMail::class, function ($mail) use ($novoUsuario) {
            return $mail->hasTo($novoUsuario->email);
        });

        // 7. Verifica se a permissão 'revistas.empresa' foi atribuída
        // Isso confirma que o método Contato::criarComUsuario rodou até o fim
        $this->assertTrue($novoUsuario->hasPermissionTo('revistas.empresa'));
    }

    /** @test */
    public function nao_pode_criar_contato_sem_email_ou_empresa()
    {
        // Tenta enviar vazio
        $response = $this->actingAs($this->adminUser)
            ->post(route('contatos.store'), [
                'name' => 'Fulano',
                'email' => '', 
                'empresa_id' => '', 
            ]);

        // Espera erros de validação do Laravel
        $response->assertSessionHasErrors(['email', 'empresa_id']);
        
        // Garante que nada foi criado no banco
        $this->assertDatabaseCount('contatos', 0);
    }
}