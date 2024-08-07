<?php


use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Exequial\ComaeTerController;
use App\Http\Controllers\Exequial\ComaeExCliController;
use App\Http\Controllers\Exequial\ComaeExRelParController;
use App\Http\Controllers\Exequial\ParentescosController;
use App\Http\Controllers\Exequial\MaeC_ExSer;
use App\Http\Controllers\Exequial\PlanController;


use App\Http\Controllers\Prueba\PruebaController;

Route::middleware(['auth'])->group(function () {
    Route::get('/', function () {
        $user = Auth::user();
        // if ($user->hasRole('creditos')) {
        //     return view('creditos.index');
        // } elseif ($user->hasRole('exequial')) {
        //     return view('exequial.asociados.index');
        // } else {
        //     return view('welcome');
        // }
        return view('exequial.asociados.index');
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

Route::resource('users', UserController::class)->names('admin.users');

//RUTAS DE EXEQUIALES
Route::get('/asociados/{id}/generarpdf', [ComaeExCliController::class, 'generarpdf'])->name('asociados.generarpdf');
Route::resource('asociados', ComaeExCliController::class)->middleware('auth')->names('exequial.asociados');

Route::get('/prestarServicio/generarpdf', [MaeC_ExSer::class, 'generarpdf'])->name('prestarServicio.generarpdf');
Route::get('/exportar-datos', [MaeC_ExSer::class, 'exportData']);
Route::get('/mes/{mes}', [MaeC_ExSer::class, 'ConsultaMes']);
Route::resource('prestarServicio', MaeC_ExSer::class);

Route::resource('beneficiarios', ComaeExRelParController::class)->names('exequial.beneficiarios');

Route::resource('terceros', ComaeTerController::class)->names('exequial.terceros');

Route::get('/parentescosall', [ParentescosController::class, 'index'])->name('exequial.parentescosall');

Route::get('/plansall', [PlanController::class, 'index'])->name('exequial.plansall');

//RUTAS

