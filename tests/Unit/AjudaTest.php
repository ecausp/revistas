<?php
use function Pest\Laravel\assertDatabaseHas;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Models\Ajuda;
use App\Enums\CategoriaAjuda;

uses(Tests\TestCase::class, RefreshDatabase::class);

test('consegue criar uma instância de ajuda com atributos', function () {
   // Ação: tenta fazer algo que, em primeiro caso, vai dar erro (RED)
   $ajuda = new Ajuda([
       'categoria' => CategoriaAjuda::Empresa,
       'descricao' => 'primeiro login do usuário',
       'url' => 'https://www.youtube.com/watch?v=ABCDE123'
   ]);

   // Validação:
   expect($ajuda->categoria)->toBe(CategoriaAjuda::Empresa)
    ->and($ajuda->descricao)->toBe('primeiro login do usuário')
    ->and($ajuda->url)->toBe('ABCDE123');
});

test('consegue gravar um registro no banco de dados', function() {
   $dados = [
    'categoria' => CategoriaAjuda::Empresa,
    'descricao' => 'primeiro login do usuário',
    'url' => 'https://www.youtube.com/watch?v=ABCDE123'
   ];

   $ajuda = Ajuda::create($dados);
   
   $this->assertDatabaseHas('ajuda', [
    'id' => $ajuda->id,
    'url' => 'ABCDE123',
   ]);
});