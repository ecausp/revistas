<?php

use App\Http\Controllers\ArquivoController;
use App\Http\Controllers\LimiteController;
use App\Http\Controllers\SolicitanteController;
use App\Http\Middleware\EnsureUserIsLogged;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LicitacaoController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\ServicoController;
use App\Http\Controllers\ContatoController;
use App\Http\Controllers\RevistaController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PedidoController;


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

Route::get('/', function () {
    return view('welcome');
})->name("home");

// Permite usar Gate::check('user')na view 404
Route::fallback(function(){
    return view('errors.404');
});

Route::resource('licitacoes', LicitacaoController::class)->parameters(['licitacoes' => 'licitacao']);
Route::resource('empresas', EmpresaController::class);

Route::resource('servicos', ServicoController::class);
Route::resource('revistas', RevistaController::class);
Route::resource('limites', LimiteController::class);
Route::resource('pedidos', PedidoController::class);
Route::resource('arquivos', ArquivoController::class);

# rota para conseguir pegar a empresa que está cadastrando esse contato;
Route::get('/contatos/create/{empresa}', ContatoController::class . '@create')->name('contatos.create');
// adds a post to the database
Route::post('/contatos', ContatoController::class .'@store')->name('contatos.store');

# rota para conseguir pegar a revista que está cadastrando esse solicitante;
Route::get('/solicitantes/create/{revista}', SolicitanteController::class . '@create')->name('solicitantes.create');
// adds a post to the database
Route::post('/solicitantes', SolicitanteController::class .'@store')->name('solicitantes.store');

// logon
Route::get('/login_empresa', [AuthController::class, 'showLogin'])->name('login')->middleware(EnsureUserIsLogged::class);
Route::post('/login_empresa', [AuthController::class, 'login'])->name('login')->middleware(EnsureUserIsLogged::class);
Route::get('/logout', function(){Auth::logout();})->middleware(EnsureUserIsLogged::class);
Route::get('/esqueci-senha', [AuthController::class, 'showForgotPassword'])->name('password.request');
Route::post('/esqueci-senha', [AuthController::class, 'sendResetLink'])->name('password.email');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');


Route::get('autocomplete', [ContatoController::class, 'autocomplete'])->name('autocomplete');
Route::get('get-user-details', [ContatoController::class, 'getUserDetails'])->name('get-user-details');

// pra colocar o valor do servico no limite
Route::get('/servicos/{id}/valor-unitario', [App\Http\Controllers\ServicoController::class, 'valorUnitario']);

// pra pegar, na criação de limite, o valor total da licitacao
Route::get('/licitacoes/{licitacao}/valor-total', function (App\Models\Licitacao $licitacao) {
    return response()->json(['valor_total' => $licitacao->valor_total]);
});



// Rota para pegar os users do banco de dados de acordo com o nome buscado
Route::get('/buscar-usuario', [SolicitanteController::class, 'buscarUsuario']);

Route::get('/arquivos/{id}/download', [ArquivoController::class, 'download'])->name('arquivos.download');
Route::get('/arquivos/{id}/ver', [ArquivoController::class, 'view'])->name('arquivos.visualizar');

Route::put('/pedidos/{pedido}/servicos', [PedidoController::class, 'updateServicos'])->name('pedidos.updateServicos');
Route::delete('/pedidos/{pedido}/servicos/{servico}', [PedidoController::class, 'removerServicoDoPedido'])->name('pedidos.servicos.remover');

// ALTERAÇÕES DE STATUS
# 1 - envia para analise da empresa
Route::post('/pedidos/{pedido}/analise-empresa', [PedidoController::class, 'paraAnaliseEmpresa'])
    ->name('pedidos.analise-empresa');

# 2 - retorna para a elaboracao
Route::post('/pedidos/{pedido}/elaboracao', [PedidoController::class, 'paraElaboracao'])
    ->name('pedidos.elaboracao');    