<?php

use App\Http\Controllers\ArquivoController;
use App\Http\Controllers\LimiteController;
use App\Http\Controllers\SolicitanteController;
use App\Http\Middleware\EnsureUserIsLogged;
use App\Models\PedidoServico; 
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LicitacaoController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\ServicoController;
use App\Http\Controllers\ContatoController;
use App\Http\Controllers\RevistaController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\SaldoController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PedidoServicoController;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Licitacao;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [HomeController::class, 'index'])->name('home');


// Permite usar Gate::check('user')na view 404
Route::fallback(function(){
    return view('errors.404');
});

// pra pegar todos os serviços que estão em um pedido em execução
Route::prefix('pedidos')->group(function () {
    Route::get('/servicos-execucao', [PedidoServicoController::class, 'index'])->name('servicos-execucao.index');
});

// para associar empresas a uma licitacao
Route::post('/licitacoes/{licitacao}/empresas', [LicitacaoController::class, 'addEmpresas'])->name('licitacoes.addEmpresas');

// para associar licitação e serviço a uma empresa:
Route::get('/empresas/{empresa}/relacionar-licitacoes-servicos', [EmpresaController::class, 'relacionarLicitacoesServicos'])
    ->name('empresas.relacionar-licitacoes-servicos')->middleware('auth');
    
// Rota para salvar relacionamento empresa -> licitação -> serviços
Route::post('/empresas/{empresa}/relacionar-licitacoes-servicos', [EmpresaController::class, 'salvarRelacionamento'])
    ->name('empresas.salvar-relacionamento')
    ->middleware('auth');


// pra pegar, na criação de limite, os limites já cadastrados pra uma revista nessa mesma licitação
Route::get('/limites/total', [LimiteController::class, 'totalPorRevistaELicitacao'])->name('limites.total');

// para acessar as ajudas:
Route::middleware(['auth'])->group(function(){
    Route::get('/empresas/ajuda', [EmpresaController::class, 'empresasAjuda'])->name('empresas.ajuda');
});

Route::resource('licitacoes', LicitacaoController::class)->parameters(['licitacoes' => 'licitacao']);

Route::middleware(['auth'])->group(function(){
    Route::resource('empresas', EmpresaController::class);
    Route::resource('servicos', ServicoController::class);
    Route::resource('revistas', RevistaController::class);
    Route::resource('limites', LimiteController::class);
    Route::resource('pedidos', PedidoController::class);
    Route::resource('arquivos', ArquivoController::class);
});


# rotas para cadastro de saldos
Route::get('/licitacoes/{licitacao}/saldos/create', [SaldoController::class, 'create'])
     ->name('saldos.create')
     ->middleware('auth');
Route::post('/saldos', [SaldoController::class, 'store'])->name('saldos.store');


// pra pegar o saldo na criação do limite
Route::get('/saldos/valor-recebido', [SaldoController::class, 'getValorRecebido'])->name('saldos.valor-recebido');

// ROTA GENÉRICA: Rotas com parâmetros dinâmicos devem vir depois
Route::get('/saldos/{saldo}', [SaldoController::class, 'show'])->name('saldos.show');


# rota para conseguir pegar a empresa que está cadastrando esse contato;
Route::get('/contatos/create/{empresa}', ContatoController::class . '@create')->name('contatos.create');
Route::post('/contatos', ContatoController::class .'@store')->name('contatos.store');

# rota para conseguir pegar a revista que está cadastrando esse solicitante;
Route::get('/solicitantes/create/{revista}', SolicitanteController::class . '@create')->name('solicitantes.create');
Route::post('/solicitantes', SolicitanteController::class .'@store')->name('solicitantes.store');

// logon
Route::get('/login_empresa', [AuthController::class, 'showLogin'])->name('login')->middleware(EnsureUserIsLogged::class);
Route::post('/login_empresa', [AuthController::class, 'login'])->name('login')->middleware(EnsureUserIsLogged::class);
Route::get('/logout', function(){Auth::logout();})->middleware(EnsureUserIsLogged::class);
Route::get('/esqueci-senha', [AuthController::class, 'showForgotPassword'])->name('password.request');
Route::post('/esqueci-senha', [AuthController::class, 'sendResetLink'])->name('password.email');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');


Route::get('/autocomplete', [ContatoController::class, 'autocomplete'])->name('autocomplete');
Route::get('get-user-details', [ContatoController::class, 'getUserDetails'])->name('get-user-details');

// para buscar a relação entre empresa, serviço e empresa na criação de limite:
Route::get('/licitacoes-servicos/dados',[\App\Http\Controllers\LicitacaoServicoController::class, 'dadosRelacionados'])
    ->name('licitacoes-servicos.dados');


// pra pegar, na criação de limite, o valor total da licitacao
Route::get('/licitacoes/{licitacao}/valor-total', function (Licitacao $licitacao) {
    return response()->json(['valor_total' => $licitacao->valor_total]);
});

// pra pegar, na criação de limite, os limites já cadastrados pra uma revista nessa mesma licitação
Route::get('/limites/total', [LimiteController::class, 'totalPorRevistaELicitacao'])->name('limites.total');


// Rota para pegar os users do banco de dados de acordo com o nome buscado
Route::get('/buscar-usuario', [SolicitanteController::class, 'buscarUsuario']);

Route::get('/arquivos/{id}/download', [ArquivoController::class, 'download'])->name('arquivos.download');
Route::get('/arquivos/{id}/ver', [ArquivoController::class, 'view'])->name('arquivos.visualizar');

Route::put('/pedidos/{pedido}/servicos', [PedidoController::class, 'updateServicos'])->name('pedidos.updateServicos');
Route::delete('/pedidos/{pedido}/servicos/{servico}', [PedidoController::class, 'removerServicoDoPedido'])->name('pedidos.servicos.remover');

Route::post('/pedidos/{pedido}/chat', [ChatController::class, 'store'])->name('chat.store');

// para adicionar arquivos novos na ediçao do pedido
Route::post('/pedidos/{pedido}/anexar-arquivos', [PedidoController::class, 'storeArquivo'])
    ->name('pedidos.store-arquivo');




// ALTERAÇÕES DE STATUS
# 1 - envia para analise da empresa
Route::post('/pedidos/{pedido}/analise-empresa', [PedidoController::class, 'paraAnaliseEmpresa'])
    ->name('pedidos.analise-empresa');

# 2 - retorna para a elaboracao
Route::post('/pedidos/{pedido}/elaboracao', [PedidoController::class, 'paraElaboracao'])
    ->name('pedidos.elaboracao');

# 3 - envia para analise do financeiro
Route::post('/pedidos/{pedido}/analise-financeiro', [PedidoController::class, 'paraAnaliseFinanceiro'])
    ->name('pedidos.analise-financeiro');

# 4 - envia o pedido para execuçao
Route::post('/pedidos/{pedido}/execucao', [PedidoController::class, 'paraExecucao'])
    ->name('pedidos.execucao');
    
# 5 - envia o pedido para conferência
Route::post('/pedidos/{pedido}/conferencia', [PedidoController::class, 'paraConferencia'])
    ->name('pedidos.conferencia');

# 6 - envia o pedido para o status em pagamento
Route::post('/pedidos/{pedido}/em-pagamento', [PedidoController::class, 'paraPagamento'])
    ->name('pedidos.em-pagamento');

# 7 - rejeita o pedido após conferencia e devolve para execucao
Route::post('/pedidos/{pedido}/devolver-execucao', [PedidoController::class, 'pedidoDevolvidoExecucao'])
    ->name('pedidos.devolver-execucao');

# 8 - conclui o pedido
Route::post('/pedidos/{pedido}/concluido', [PedidoController::class, 'pedidoConcluido'])
    ->name('pedidos.concluido');
