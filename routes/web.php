<?php

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
Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');



// mail route
Route::get('/send-test-email', function () {
    Mail::to('destinatario@teste.com')->send(new TestMail());
    return 'E-mail de teste enviado com sucesso!';
});