<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ComaeExCliController;
use App\Http\Controllers\ComaeExRelParController;
use App\Http\Controllers\ParentescosController;
use App\Http\Controllers\ExMonitoriaController;


Route::middleware(['auth'])->group(function () {
    Route::get('/', function () {
        return view('dashboardTemplate');
    })->name('dashboard');
});


Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});


Route::get('/asociados/{id}/generarpdf', [ComaeExCliController::class, 'generarpdf'])->name('asociados.generarpdf');
Route::get('/monitoria/generarpdf', [ExMonitoriaController::class, 'generarpdf'])->name('monitoria.generarpdf');

Route::resource('asociados', ComaeExCliController::class)->middleware('auth');
Route::resource('beneficiarios', ComaeExRelParController::class);
Route::resource('monitoria', ExMonitoriaController::class);

Route::get('/parentescosall', [ParentescosController::class, 'index'])->name('parentescosall');
Route::get('/exportar-datos', [ExMonitoriaController::class, 'exportData']);
Route::get('/mes/{mes}', [ExMonitoriaController::class, 'ConsultaMes']);  

