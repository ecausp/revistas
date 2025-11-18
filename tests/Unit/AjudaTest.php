<?php
use App\Models\Ajuda;
use App\Enums\CategoriaAjuda;

test('consegue criar uma instância de ajuda com atributos', function () {
   // Ação: tentar criar uma instancia da model que ainda não existe:
   $ajuda = new Ajuda([
       'categoria' => 'empresa',
       'descricao' => 'primeiro login do usuário',
       'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ'
   ]);

   // Validação:
   expect($ajuda->categoria)->toBe('empresa')
    ->and($ajuda->descricao)->toBe('primeiro login do usuário')
    ->and($ajuda->url)->toBe('https://www.youtube.com/watch?v=dQw4w9WgXcQ');
});
