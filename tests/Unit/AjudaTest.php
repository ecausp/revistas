<?php
use App\Models\Ajuda;
use App\Enums\CategoriaAjuda;

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
