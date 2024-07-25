<?php

use App\Http\Controllers\ComaeTerController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ComaeExCliController;
use App\Http\Controllers\ComaeExRelParController;
use App\Http\Controllers\ParentescosController;
use App\Http\Controllers\ExMonitoriaController;
use App\Http\Controllers\PlanController;

Route::middleware(['auth'])->group(function () {
    Route::get('/', function () {
        $user = Auth::user();
        if ($user->hasRole('creditos')) {
            return view('creditos.index');
        } elseif ($user->hasRole('exequial')) {
            return view('exequial.asociados.index');
        } else {
            return view('welcome');
        }
    })->name('dashboard');
});

Route::get('/offline', function () {
    return view('vendor.laravelpwa.offline');
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

Route::get('/prueba', [ParentescosController::class, 'index']);
Route::get('/asociados/{id}/generarpdf', [ComaeExCliController::class, 'generarpdf'])->name('asociados.generarpdf');
Route::get('/prestarServicio/generarpdf', [ExMonitoriaController::class, 'generarpdf'])->name('prestarServicio.generarpdf');

Route::resource('asociados', ComaeExCliController::class)->middleware('auth');
Route::resource('beneficiarios', ComaeExRelParController::class);
Route::resource('terceros', ComaeTerController::class);
Route::resource('prestarServicio', ExMonitoriaController::class);

Route::get('/parentescosall', [ParentescosController::class, 'index'])->name('parentescosall');
Route::get('/plansall', [PlanController::class, 'index'])->name('plansall');
Route::get('/exportar-datos', [ExMonitoriaController::class, 'exportData']);
Route::get('/mes/{mes}', [ExMonitoriaController::class, 'ConsultaMes']);  

