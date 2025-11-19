<?php

use App\Models\Ajuda;
use App\Models\User;
use App\Enums\CategoriaAjuda;

use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\post;
use function Pest\Laravel\actingAs;

// limpa o banco de dados a cada teste
uses(Tests\TestCase::class, RefreshDatabase::class);

test('deve permitir cadastrar uma nova ajuda pela rota', function(){
    $dados = [
        'categoria' => CategoriaAjuda::Empresa->value,
        'descricao' => 'primeiro login do usuário',
        'url' => 'https://www.youtube.com/watch?v=ABCDE123'
    ];

    post('/ajuda', $dados)->assertStatus(302);

    assertDatabaseHas('ajuda', [
        'categoria' => $dados['categoria'],
        'descricao' => $dados['descricao'],
        'url' => 'ABCDE123',
    ]);
});

test('não deve permitir cadastro de dados invalidos', function()
{
    $dadosInvalidos = [
        'categoria' => CategoriaAjuda::Empresa->value,
        'descricao' => 'primeiro login do usuário',
        'url' => '',
    ];

    post('/ajuda', $dadosInvalidos)->assertSessionHasErrors(['url']);
});

test('deve exibir a tela principal de ajuda', function() {
    $response = $this->get('/ajuda');

    $response->assertStatus(200)
        ->assertViewIs('ajuda.index');
});

test('deve exibir o index de ajudas com a lista de vídeos', function() {
   Ajuda::create([
    'categoria' => CategoriaAjuda::Empresa->value,
    'descricao' => "Video Exclusivo de Teste",
    'url' => 'https://youtube.com/watch?v=TESTE123',
   ]); 

   $response = $this->get('/ajuda');

   $response->assertStatus(200)
    ->assertViewHas('ajudas')
    ->assertSee("Video Exclusivo de Teste");
});

test('deve exibir a tela de cadastro de ajuda', function() {
    $response = $this->get('/ajuda/create');

    $response->assertStatus(200)
        ->assertViewIs('ajuda.create'); 
});

test('deve exibir o forms de edição com os dados já carregados', function()
{
    $ajuda = Ajuda::create([
        'categoria' => CategoriaAjuda::Empresa->value,
        'descricao' => "Video Para Edição",
        'url' => 'https://youtube.com/watch?v=TESTE123',
    ]);

    $response = $this->get("/ajuda/{$ajuda->id}/edit");

    $response->assertStatus(200)
        ->assertViewIs('ajuda.edit')
        ->assertSee("Video Para Edição");
});

test('deve atualizar uma ajuda existente no banco de dados', function()
{
    $ajuda = Ajuda::create([
        'categoria' => CategoriaAjuda::Empresa->value,
        'descricao' => "Video Para Edição",
        'url' => 'https://youtube.com/watch?v=TESTE123',
    ]);

    $novosDados = [
        'categoria' => CategoriaAjuda::Financeiro->value,
        'descricao' => 'Video Atualizado',
        'url' => 'https://youtube.com/watch?v=NOVO123'
    ];

    $this->put("/ajuda/{$ajuda->id}", $novosDados)
        ->assertStatus(302); // redireciona para a rota de index

    $this->assertDatabaseHas('ajuda', [
        'id' => $ajuda->id,
        'categoria' => $novosDados['categoria'],
        'descricao' => $novosDados['descricao'],
        'url' => 'NOVO123',
    ]);

    $this->assertDatabaseMissing('ajuda', [ // garante que os dados antigos não existem mais
        'descricao' => 'Video Para Edição' 
    ]);
});

test('deve excluir uma ajuda do banco de dados', function()
{
    $ajuda = Ajuda::create([
        'categoria' => CategoriaAjuda::Empresa->value,
        'descricao' => "Video Para Exclusão",
        'url' => 'https://youtube.com/watch?v=DELETE',
    ]);

    $this->delete("/ajuda/{$ajuda->id}")
        ->assertStatus(302); // redireciona para a rota de index
   
    $this->assertDatabaseMissing('ajuda', [
        'id' => $ajuda->id,
    ]);
});

test('apenas o admin pode acessar a criação e salvar ajudas', function(){

    // limpa o cache, sempre tem que usar pra testes com Spatie
    app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

    $role = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
    $admin = User::factory()->create();
    $admin->assignRole($role);

    actingAs($admin)
        ->get('/ajuda/create')
        ->assertStatus(200);

    $dados = [
        'categoria' => CategoriaAjuda::Empresa->value,
        'descricao' => "Video de Teste do Admin",
        'url' => "https://youtube.com/watch?v=ADMIN"
    ];

    actingAs($admin)
        ->post('/ajuda', $dados)
        ->assertStatus(302);
});

test('usuario comum não pode criar nem salvar ajudas', function(){
    $userComum = User::factory()->create();

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