<?php

use App\Models\Ajuda;
use App\Enums\CategoriaAjuda;

use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\post;

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