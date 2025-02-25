<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LicitacaoController;

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

Route::get('licitacoes/create', [LicitacaoController::class, 'create'])->name('licitacoes.create');
Route::post('licitacoes', [LicitacaoController::class, 'store'])->name('licitacoes.store');
Route::get('licitacoes', [LicitacaoController::class, 'index'])->name('licitacoes.index');
Route::get('licitacoes/{licitacao}', [LicitacaoController::class, 'show'])->name('licitacoes.show');
Route::get('licitacoes/{licitacao}/edit', [LicitacaoController::class, 'edit'])->name('licitacoes.edit');
Route::put('licitacoes/{licitacao}', [LicitacaoController::class, 'update'])->name('licitacoes.update');