<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ComaeTerController;
use App\Http\Controllers\ComaeExRelParController;
use App\Http\Controllers\ParentescosController;
use App\Http\Controllers\MonitoriaExController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/asociados/{id}/generarpdf', [ComaeTerController::class, 'generarpdf'])->name('asociados.generarpdf');
Route::get('/monitoria/generarpdf', [MonitoriaExController::class, 'generarpdf'])->name('monitoria.generarpdf');

Route::resource('asociados', ComaeTerController::class)->middleware('auth');
Route::resource('beneficiarios', ComaeExRelParController::class);
Route::resource('monitoria', MonitoriaExController::class);

Route::get('/parentescosall', [ParentescosController::class, 'index'])->name('parentescosall');
Route::get('/exportar-datos', [MonitoriaExController::class, 'exportData']);
Route::get('/mes/{mes}', [MonitoriaExController::class, 'ConsultaMes']);
