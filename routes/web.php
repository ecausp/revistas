<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LicitacaoController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\ServicoController;
use App\Http\Controllers\ContatoController;
use App\Http\Controllers\RevistaController;
use App\Http\Controllers\AuthController;

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
});

// Permite usar Gate::check('user')na view 404
Route::fallback(function(){
    return view('errors.404');
});

Route::resource('licitacoes', LicitacaoController::class)->parameters(['licitacoes' => 'licitacao']);
Route::resource('empresas', EmpresaController::class);
Route::resource('servicos', ServicoController::class);
Route::resource('revistas', RevistaController::class);

# rota para conseguir pegar a empresa que está cadastrando esse contato;
Route::get('/contatos/create/{empresa}', ContatoController::class . '@create')->name('contatos.create');
// adds a post to the database
Route::post('/contatos', ContatoController::class .'@store')->name('contatos.store');

// logon
Route::get('/login_laravel', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login_laravel', [AuthController::class, 'login'])->name('login');
Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
Route::post('/logout_laravel', [AuthController::class, 'logout'])->name('logout');