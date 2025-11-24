<?php

use App\Models\Ajuda;
use App\Models\User;
use App\Enums\CategoriaAjuda;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\post;
use function Pest\Laravel\actingAs;

uses(Tests\TestCase::class, RefreshDatabase::class);

test('consegue visualizar o index de ajudas', function() {
   Ajuda::create([
       'categoria' => CategoriaAjuda::Empresa->value,
       'descricao' => "Video Exclusivo de Teste",
       'url' => 'https://youtube.com/watch?v=TESTE123',
   ]); 

   $this->get('/ajuda')
        ->assertStatus(200)
        ->assertViewIs('ajuda.index')
        ->assertSee("Video Exclusivo de Teste");
});

test('admin consegue visualizar o formulário de criação', function() {
    $admin = criarAdmin();

    actingAs($admin)
        ->get('/ajuda/create')
        ->assertStatus(200)
        ->assertViewIs('ajuda.create'); 
});

test('admin consegue salvar uma nova ajuda', function(){
    $admin = criarAdmin();

    $dados = [
        'categoria' => CategoriaAjuda::Empresa->value,
        'descricao' => 'Tutorial Admin',
        'url' => 'https://www.youtube.com/watch?v=ABCDE123'
    ];

    actingAs($admin)
        ->post('/ajuda', $dados)
        ->assertStatus(302);

    assertDatabaseHas('ajuda', [
        'url' => 'ABCDE123',
    ]);
});

test('admin consegue visualizar o formulário de edição com dados', function()
{
    $admin = criarAdmin();
    $ajuda = Ajuda::create([
        'categoria' => CategoriaAjuda::Empresa->value,
        'descricao' => "Video Para Edição",
        'url' => 'https://youtube.com/watch?v=TESTE123',
    ]);

    actingAs($admin)
        ->get("/ajuda/{$ajuda->id}/edit")
        ->assertStatus(200)
        ->assertViewIs('ajuda.edit')
        ->assertSee("Video Para Edição");
});

test('admin consegue atualizar uma ajuda existente', function()
{
   $admin = criarAdmin();
   $ajuda = Ajuda::create([
       'categoria' => CategoriaAjuda::Empresa->value,
       'descricao' => "Descrição Antiga",
       'url' => "https://youtube.com/watch?v=ANTIGO",
   ]);

   $novosDados = [
     'categoria' => CategoriaAjuda::Financeiro->value,
     'descricao' => "Descrição Nova",
     'url' => "https;//youtube.com/watch?v=NOVA"  
   ];

   actingAs($admin)
        ->put("/ajuda/{$ajuda->id}", $novosDados)
        ->assertStatus(302);

   $this->assertDatabaseHas('ajuda', ['url' => 'NOVA']);
   $this->assertDatabaseMissing('ajuda', ['descricao' => "Descrição Antiga"]);
});

test('admin consegue excluir uma ajuda', function()
{
    $admin = criarAdmin();
    $ajuda = Ajuda::create([
        'categoria' => CategoriaAjuda::Empresa->value,
        'descricao' => "Video Para Exclusão",
        'url' => 'https://youtube.com/watch?v=DELETE',
    ]);

    actingAs($admin)
        ->delete("/ajuda/{$ajuda->id}")
        ->assertStatus(302);
   
    $this->assertDatabaseMissing('ajuda', ['id' => $ajuda->id]);
});

test('não deve permitir cadastro com dados inválidos (validação)', function()
{
    $admin = criarAdmin();

    $dadosInvalidos = [
        'categoria' => CategoriaAjuda::Empresa->value,
        'descricao' => 'Sem URL',
        'url' => '',
    ];

    actingAs($admin)
        ->post('/ajuda', $dadosInvalidos)
        ->assertSessionHasErrors(['url']);
});

test('usuario comum não pode criar nem salvar ajudas', function(){
    // 1. Garante que a permissão "admin" existe no banco para o Menu USP não quebrar (Erro 500)
    app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    Permission::firstOrCreate(['name' => 'admin', 'guard_name' => 'senhaunica']);
    
    // 2. Cria usuário comum
    $userComum = User::create([
        'name' => "Usuario Comum",
        'email' => "comum@teste.com",
        'password' => bcrypt("123456"),
        'codpes' => "132456",
        'telefone' => "123456",
    ]);

    actingAs($userComum)
        ->get('/ajuda/create')
        ->assertStatus(403); 
    $dados = [
        'categoria' => CategoriaAjuda::Empresa->value,
        'descricao' => 'Tentativa de Hack',
        'url' => 'https://youtube.com/watch?v=HACK'
    ];

    actingAs($userComum)
        ->post('/ajuda', $dados)
        ->assertStatus(403);
});

test('usuario comum não pode editar nem atualizar ajudas', function(){
    app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    Permission::firstOrCreate(['name' => 'admin', 'guard_name' => 'senhaunica']);

    $userComum = User::create([
        'name' => "Usuario Comum",
        'email' => "comum_edit@teste.com",
        'password' => bcrypt("123456"),
        'codpes' => "111222",
        'telefone' => "111222",
    ]);

    $ajuda = Ajuda::create([
        'categoria' => CategoriaAjuda::Empresa->value,
        'descricao' => "Video Protegido",
        'url' => 'https://youtube.com/watch?v=PROTECTED',
    ]);

    actingAs($userComum)
        ->get("/ajuda/{$ajuda->id}/edit")
        ->assertStatus(403);

    $dadosHack = [
        'categoria' => CategoriaAjuda::Financeiro->value,
        'descricao' => 'Hacked',
        'url' => 'https://youtube.com/watch?v=HACK'
    ];

    actingAs($userComum)
        ->put("/ajuda/{$ajuda->id}", $dadosHack)
        ->assertStatus(403);
        
    assertDatabaseHas('ajuda', ['descricao' => 'Video Protegido']);
});

test('usuario comum não pode deletar uma ajuda', function(){
    app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    Permission::firstOrCreate(['name' => 'admin', 'guard_name' => 'senhaunica']);

    $userComum = User::create([
        'name' => "Usuario Comum",
        'email' => "comum_del@teste.com",
        'password' => bcrypt("123456"),
        'codpes' => "333444",
        'telefone' => "333444",
    ]);

    $ajuda = Ajuda::create([
        'categoria' => CategoriaAjuda::Empresa->value,
        'descricao' => "Video Intocavel",
        'url' => 'https://youtube.com/watch?v=SAFE',
    ]);

    actingAs($userComum)
        ->delete("/ajuda/{$ajuda->id}")
        ->assertStatus(403);

    assertDatabaseHas('ajuda', ['id' => $ajuda->id]);
});


// ==========================================
// HELPERS
// ==========================================

function criarAdmin() {
    // 1. Permission (Para o Tema USP)
    $permission = Permission::firstOrCreate([
        'name' => 'admin', 
        'guard_name' => 'senhaunica'
    ]);

    // 2. Role (Para o Controller)
    $role = Role::firstOrCreate([
        'name' => 'admin', 
        'guard_name' => 'senhaunica'
    ]);

    // 3. User
    $user = User::create([
        'name' => 'Admin Teste',
        'email' => 'admin_' . uniqid() . '@teste.com',
        'password' => bcrypt('123456'),
        'codpes' => rand(10000, 99999),
        'telefone' => '1199999999',
    ]);

    // 4. Vínculos
    $role->givePermissionTo($permission);
    $user->assignRole($role);

    return $user;
}