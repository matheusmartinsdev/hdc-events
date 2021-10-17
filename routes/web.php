<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventController;
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

Route::get('/', [EventController::class, 'index']);

Route::get('/eventos/criar', [EventController::class, 'create'])->middleware('auth');

Route::get('/eventos/{id}', [EventController::class, 'show']);

Route::get('/eventos/editar/{id}', [EventController::class, 'edit'])->middleware('auth');

Route::put('/eventos/atualizar/{id}', [EventController::class, 'update'])->middleware('auth');

Route::delete('/eventos/{id}', [EventController::class, 'destroy'])->middleware('auth');

Route::post('/eventos', [EventController::class, 'store']);

Route::get('/contato', [EventController::class, 'contact']);

Route::get('/dashboard', [EventController::class, 'dashboard'])->middleware('auth');

Route::post('/eventos/participar/{id}', [EventController::class, 'joinEvent'])->middleware('auth');