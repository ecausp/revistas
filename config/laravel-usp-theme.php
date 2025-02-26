<?php

$submenu1 = [
    [
        'type' => 'header',
        'text' => '<i class="fa fa-shopping-cart" aria-hidden="true"></i> Licitações',
        'can' => 'admin'
    ],
    [
        'text' => '<i class="fas fa-plus-circle aria-hidden="true"></i> Cadastrar',
        'url' => 'licitacoes/create',
        'can' => 'admin'
    ],
    [
        'text' => '<i class="fas fa-list-ul"></i> Listar',
        'url' => 'licitacoes',
        'can' => 'admin'
    ],
    [
        'type' => 'divider',
    ],
    [
        'type' => 'header',
        'text' => '<i class="fa fa-building" aria-hidden="true"></i> Empresas',
        'can' => 'admin'
    ],
    [
        'text' => '<i class="fas fa-plus-circle aria-hidden="true"></i> Cadastrar',
        'url' => 'empresas/create',
        'can' => 'admin'
    ],
    [
        'text' => '<i class="fas fa-list-ul"></i> Listar',
        'url' => 'empresas',
        'can' => 'admin'
    ],
    [
        'type' => 'divider',
    ],
    [
        'type' => 'header',
        'text' => '<i class="fa fa-wrench" aria-hidden="true"></i> Serviços',
        'can' => 'admin'
    ],
    [
        'text' => '<i class="fas fa-plus-circle aria-hidden="true"></i> Cadastrar',
        'url' => 'servicos/create',
        'can' => 'admin'
    ],
    [
        'text' => '<i class="fas fa-list-ul"></i> Listar',
        'url' => 'servicos',
        'can' => 'admin'
    ],
    [
        'type' => 'divider',
    ],
    [
        'type' => 'header',
        'text' => '<i class="fa fa-book" aria-hidden="true"></i> Revistas',
        'can' => 'admin'
    ],
    [
        'text' => '<i class="fas fa-plus-circle aria-hidden="true"></i> Cadastrar',
        'url' => 'revistas/create',
        'can' => 'admin'
    ],
    [
        'text' => '<i class="fas fa-list-ul"></i> Listar',
        'url' => 'revistas',
        'can' => 'admin'
    ],
    [
        'type' => 'divider',
    ],
    [
        'type' => 'header',
        'text' => '<i class="fas fa-money" aria-hidden="true"></i> Limites',
        'can' => 'admin'
    ],
    [
        'text' => '<i class="fas fa-plus-circle aria-hidden="true"></i> Cadastrar',
        'url' => 'limites/create',
        'can' => 'admin'
    ],
    [
        'text' => '<i class="fas fa-list-ul"></i> Listar',
        'url' => 'limites',
        'can' => 'admin'
    ],
];

$menu = [
    [
        'text' => '<i class="fas fa-home"></i> Home',
        'url' => 'home',
    ],
    [
        # este item de menu será substituido no momento da renderização
        'key' => 'menu_dinamico',
    ],
    [
        'text' => 'Administração',
        'submenu' => $submenu1,
        'can' => 'admin',
    ],
    [
        'text' => 'Está logado',
        'url' => config('app.url') . '/logado', // com caminho absoluto
        'can' => 'user',
    ],
];

$right_menu = [
    [
        'key' => 'senhaunica-socialite',
    ],
    [
        'key' => 'laravel-tools',
    ],
    [
        'text' => '<i class="fas fa-cog"></i>',
        'title' => 'Configurações',
        'target' => '_blank',
        'url' => config('app.url') . '/item1',
        'align' => 'right',
    ],
];


return [
    # valor default para a tag title, dentro da section title.
    # valor pode ser substituido pela aplicação.
    'title' => config('app.name'),

    # USP_THEME_SKIN deve ser colocado no .env da aplicação
    'skin' => env('USP_THEME_SKIN', 'uspdev'),

    # chave da sessão. Troque em caso de colisão com outra variável de sessão.
    'session_key' => 'laravel-usp-theme',

    # usado na tag base, permite usar caminhos relativos nos menus e demais elementos html
    # na versão 1 era dashboard_url
    'app_url' => config('app.url'),

    # login e logout
    'logout_method' => 'POST',
    'logout_url' => 'logout',
    'login_url' => 'login',

    # menus
    'menu' => $menu,
    'right_menu' => $right_menu,

    # mensagens flash - https://uspdev.github.io/laravel#31-mensagens-flash
    'mensagensFlash' => true,

    # container ou container-fluid
    'container' => 'container-fluid',
];
