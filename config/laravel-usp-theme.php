<?php

$submenuAjuda = [
    [
        'text' => '<i class="fas fa-building" aria-hidden="true"></i> Empresa',
        'url' => 'ajuda/empresas',
    ],
    [
        'text' => '<i class="fas fa-calculator" aria-hidden="true"></i> Financeiro',
        'url' => 'ajuda/financeiro',
    ],
    [
        'text' => '<i class="fas fa-book" aria-hidden="true"></i> Revista',
        'url' => 'ajuda/revista',
    ],

];

$submenuPedidos = [
    [
        'type' => 'header',
        'text' => '<i class="fa fa-shopping-cart" aria-hidden="true"></i> Pedidos',
    ],
    [
        'text' => '<i class="fas fa-plus-circle" aria-hidden="true"></i> Cadastrar',
        'url' => 'pedidos/create',
        'can' => 'acesso-revista'
    ],
    [
        'text' => '<i class="fas fa-list-ul" aria-hidden="true"></i> Listar',
        'url' => 'pedidos',
    ],
    [
        'type' => 'divider',
    ],
    [
        'type' => 'header',
        'text' => '<i class="fas fa-tasks" aria-hidden="true"></i> Serviços',
    ],
    [
        'text' => '<i class="fas fa-plus-circle" aria-hidden="true"></i> Cadastrar',
        'url' => 'servicos/create',
        'can' => 'admin-financeiro'
    ],
    [
        'text' => '<i class="fas fa-list-ul" aria-hidden="true"></i> Listar',
        'url' => 'servicos-licitacoes',
        'can' => 'acesso-empresa'
    ],
    [
        'text' => '<i class="fas fa-cog" aria-hidden="true"></i> Em Execução',
        'url' => 'pedidos/servicos-execucao',
    ],

];

$submenu1 = [
    [
        'type' => 'header',
        'text' => '<i class="fa fa-shopping-cart" aria-hidden="true"></i> Licitações',
        'can' => 'acesso-revista'
    ],
    [
        'text' => '<i class="fas fa-plus-circle" aria-hidden="true"></i> Cadastrar',
        'url' => 'licitacoes/create',
        'can' => 'admin-financeiro'
    ],
    [
        'text' => '<i class="fas fa-list-ul" aria-hidden="true"></i> Listar',
        'url' => 'licitacoes',
        'can' => 'acesso-revista'
    ],
    [
        'type' => 'divider',
        'can' => 'admin-financeiro'
    ],
    [
        'type' => 'header',
        'text' => '<i class="fa fa-building" aria-hidden="true"></i> Empresas',
        'can' => 'acesso-empresa'
    ],
    [
        'text' => '<i class="fas fa-plus-circle" aria-hidden="true"></i> Cadastrar',
        'url' => 'empresas/create',
        'can' => 'admin-financeiro'
    ],
    [
        'text' => '<i class="fas fa-list-ul" aria-hidden="true"></i> Listar',
        'url' => 'empresas',
        'can' => 'acesso-empresa'
    ],
    [
        'type' => 'divider',
        'can' => 'admin-financeiro'
    ],
    [
        'type' => 'header',
        'text' => '<i class="fas fa-tasks" aria-hidden="true"></i> Serviços',
        'can' => 'admin-financeiro'
    ],
    [
        'text' => '<i class="fas fa-plus-circle" aria-hidden="true"></i> Cadastrar',
        'url' => 'servicos/create',
        'can' => 'admin-financeiro'
    ],
    [
        'text' => '<i class="fas fa-list-ul" aria-hidden="true"></i> Listar',
        'url' => 'servicos',
        'can' => 'admin-financeiro'
    ],
    [
        'type' => 'divider',
        'can' => 'acesso-empresa'
    ],
    [
        'type' => 'header',
        'text' => '<i class="fa fa-book" aria-hidden="true"></i> Revistas',
        'can' => 'acesso-revista'
    ],
    [
        'text' => '<i class="fas fa-plus-circle" aria-hidden="true"></i> Cadastrar',
        'url' => 'revistas/create',
        'can' => 'admin-financeiro'
    ],
    [
        'text' => '<i class="fas fa-list-ul" aria-hidden="true"></i> Listar',
        'url' => 'revistas',
        'can' => 'acesso-revista'
    ],
    [
        'type' => 'divider',
        'can' => 'admin-financeiro'
    ],
    [
        'type' => 'header',
        'text' => '<i class="fas fa-money-bill-alt" aria-hidden="true"></i> Limites',
        'can' => 'admin-financeiro'
    ],
    [
        'text' => '<i class="fas fa-plus-circle" aria-hidden="true"></i> Cadastrar',
        'url' => 'limites/create',
        'can' => 'admin-financeiro'
    ],
    [
        'text' => '<i class="fas fa-list-ul" aria-hidden="true"></i> Listar',
        'url' => 'limites',
        'can' => 'admin-financeiro'
    ],
];

$menu = [
    [
        # este item de menu será substituido no momento da renderização
        'key' => 'menu_dinamico',
        'can' => 'logado',
    ],
    [
        'text' => '<i class="fas fa-tools"></i> Financeiro',
        'submenu' => $submenu1,
        'can' => 'logado',
    ],
    [
        'text' => '<i class="fas fa-tools"></i> Pedidos',
        'submenu' => $submenuPedidos,
        'can' => 'logado',
    ],
];



$right_menu = [
    [
        'text' => '<i class="fas fa-question-circle"></i> Ajuda',
        'submenu' => $submenuAjuda,
    ],
    [
        'key' => 'senhaunica-socialite',
    ],
    [
        'key' => 'laravel-tools',
    ],
    [
        'text' => '<span class="badge badge-danger">Admin</span>',
        'can' => 'admin',
    ],
    [
        'text' => '<span class="badge badge-info">Financeiro</span>',
        'can' => 'badge-financeiro',
    ],
    [
        'text' => '<span class="badge badge-success">Empresa</span>',
        'can' => 'badge-empresa',
    ],
    [
        'text' => '<span class="badge badge-warning">Revista</span>',
        'can' => 'badge-revista',
    ],


    // [
    //     'text' => '<i class="fas fa-cog" aria-hidden="true"></i>',
    //     'title' => 'Configurações',
    //     'target' => '_blank',
    //     'url' => config('app.url') . '/item1',
    //     'align' => 'right',
    // ],
];

$cadastrosAuxiliaresUrl = rtrim((string) env('CADASTROS_AUXILIARES_URL', ''), '/');
$cadastrosAuxiliaresMensagensEndpoint = $cadastrosAuxiliaresUrl !== ''
    ? $cadastrosAuxiliaresUrl . '/api/mensagens'
    : '';


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

    # integração opcional com endpoint de mensagens do cadastros-auxiliares
    'cadastros_auxiliares_mensagens_integracao' => env('CADASTROS_AUXILIARES_MENSAGENS_INTEGRACAO', false),
    'cadastros_auxiliares_mensagens_endpoint_url' => env('CADASTROS_AUXILIARES_MENSAGENS_ENDPOINT_URL', $cadastrosAuxiliaresMensagensEndpoint),
    'cadastros_auxiliares_mensagens_sistema' => env('CADASTROS_AUXILIARES_SISTEMA_NAME', ''),
    'cadastros_auxiliares_mensagens_limite' => (int) env('CADASTROS_AUXILIARES_MENSAGENS_LIMITE', 5),
    'cadastros_auxiliares_mensagens_timeout' => (int) env('CADASTROS_AUXILIARES_MENSAGENS_TIMEOUT', 5),
    'cadastros_auxiliares_mensagens_refresh' => (int) env('CADASTROS_AUXILIARES_MENSAGENS_REFRESH', 30),
    'cadastros_auxiliares_password' => env('CADASTROS_AUXILIARES_PASSWORD', ''),

    # container ou container-fluid
    'container' => 'container-fluid',
];
