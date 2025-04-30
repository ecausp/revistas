<?php

use App\Http\Controllers\LimiteController;
use App\Http\Middleware\EnsureUserIsLogged;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LicitacaoController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\ServicoController;
use App\Http\Controllers\ContatoController;
use App\Http\Controllers\RevistaController;
use App\Http\Controllers\AuthController;


use Illuminate\Support\Facades\Mail;
use App\Mail\TestMail;

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


# rota para conseguir pegar a empresa que está cadastrando esse contato;
Route::get('/contatos/create/{empresa}', ContatoController::class . '@create')->name('contatos.create');
// adds a post to the database
Route::post('/contatos', ContatoController::class .'@store')->name('contatos.store');

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