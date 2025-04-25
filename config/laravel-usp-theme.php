<?php

$submenu1 = [
    [
        'type' => 'header',
        'text' => '<i class="fa fa-shopping-cart" aria-hidden="true"></i> Licitações',
        'can' => 'licitacoes'
    ],
    [
        'text' => '<i class="fas fa-plus-circle" aria-hidden="true"></i> Cadastrar',
        'url' => 'licitacoes/create',
        'can' => 'licitacoes'
    ],
    [
        'text' => '<i class="fas fa-list-ul" aria-hidden="true"></i> Listar',
        'url' => 'licitacoes',
        'can' => 'licitacoes'
    ],
    [
        'type' => 'divider',
        'can' => 'licitacoes'
    ],
    [
        'type' => 'header',
        'text' => '<i class="fa fa-building" aria-hidden="true"></i> Empresas',
        'can' => 'empresas'
    ],
    [
        'text' => '<i class="fas fa-plus-circle" aria-hidden="true"></i> Cadastrar',
        'url' => 'empresas/create',
        'can' => 'empresas'
    ],
    [
        'text' => '<i class="fas fa-list-ul" aria-hidden="true"></i> Listar',
        'url' => 'empresas',
        'can' => 'empresas'
    ],
    [
        'type' => 'divider',
        'can'=>'empresas'
    ],
    [
        'type' => 'header',
        'text' => '<i class="fas fa-tasks" aria-hidden="true"></i> Serviços',
        'can' => 'servicos'
    ],
    [
        'text' => '<i class="fas fa-plus-circle" aria-hidden="true"></i> Cadastrar',
        'url' => 'servicos/create',
        'can' => 'servicos'
    ],
    [
        'text' => '<i class="fas fa-list-ul" aria-hidden="true"></i> Listar',
        'url' => 'servicos',
        'can' => 'servicos'
    ],
    [
        'type' => 'divider',
        'can' => 'servicos'
    ],
    [
        'type' => 'header',
        'text' => '<i class="fa fa-book" aria-hidden="true"></i> Revistas',
        'can' => 'revistas'
    ],
    [
        'text' => '<i class="fas fa-plus-circle" aria-hidden="true"></i> Cadastrar',
        'url' => 'revistas/create',
        'can' => 'revistas'
    ],
    [
        'text' => '<i class="fas fa-list-ul" aria-hidden="true"></i> Listar',
        'url' => 'revistas',
        'can' => 'revistas'
    ],
    [
        'type' => 'divider',
        'can' => 'revistas'
    ],
    [
        'type' => 'header',
        'text' => '<i class="fas fa-money-bill-alt" aria-hidden="true"></i> Limites',
        'can' => 'limites'
    ],
    [
        'text' => '<i class="fas fa-plus-circle" aria-hidden="true"></i> Cadastrar',
        'url' => 'limites/create',
        'can' => 'limites'
    ],
    [
        'text' => '<i class="fas fa-list-ul" aria-hidden="true"></i> Listar',
        'url' => 'limites',
        'can' => 'limites'
    ],
];

$menu = [
    [
        # este item de menu será substituido no momento da renderização
        'key' => 'menu_dinamico',
    ],
    [
        'text' => '<i class="fas fa-tools"></i> Administração',
        'submenu' => $submenu1,
        'can' => '',
    ],
];

$right_menu = [
    [
        'key' => 'senhaunica-socialite',
    ],
    [
        'key' => 'laravel-tools',
    ],
    // [
    //     'text' => '<i class="fas fa-cog" aria-hidden="true"></i>',
    //     'title' => 'Configurações',
    //     'target' => '_blank',
    //     'url' => config('app.url') . '/item1',
    //     'align' => 'right',
    // ],
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