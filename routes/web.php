<?php


use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Cartera\ReadExelController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuditoriaController;

use App\Http\Controllers\Exequial\ComaeTerController;
use App\Http\Controllers\Exequial\ComaeExCliController;
use App\Http\Controllers\Exequial\ComaeExRelParController;
use App\Http\Controllers\Exequial\ParentescosController;
use App\Http\Controllers\Exequial\MaeC_ExSerController;
use App\Http\Controllers\Exequial\PlanController;

use App\Http\Controllers\Seguros\SegPolizaController;


/* Route::middleware(['auth'])->group(function () {
    Route::get('/', function () {
        //return view('exequial.asociados.index');
    })->name('dashboard');
}); */
Route::middleware(['auth'])->group(function () {
    Route::get('/', [AuditoriaController::class, 'index'])->name('dashboard');
});

Route::get('/offline', function () {
    return view('vendor.laravelpwa.offline');
});

Route::get('/base', function () {
    return view('layouts.base');
});


// Route::middleware([
//     'auth:sanctum',
//     config('jetstream.auth_session'),
//     'verified',
// ])->group(function () {
//     Route::get('/dashboard', function () {
//         return view('dashboard');
//     })->name('dashboard');
// });

//ADMIN
Route::resource('users', UserController::class)->middleware('can:admin')->names('admin.users');

//RUTAS DE EXEQUIALES
Route::get('/asociados/{id}/generarpdf', [ComaeExCliController::class, 'generarpdf'])->middleware('auth')->name('asociados.generarpdf');
Route::resource('asociados', ComaeExCliController::class)->middleware('auth')->names('exequial.asociados');

Route::get('/prestarServicio/generarpdf', [MaeC_ExSerController::class, 'generarpdf'])->middleware('auth')->name('prestarServicio.generarpdf');
Route::resource('prestarServicio', MaeC_ExSerController::class)->middleware('auth')->names('exequial.prestarServicio');
Route::get('/prestarServicio/{id}/generarpdf', [MaeC_ExSerController::class, 'reporteIndividual'])->name('prestarServicio.repIndividual');
Route::get('/exportar-datos', [MaeC_ExSerController::class, 'exportData']);
Route::get('/prestarServicio/mes/{mes}', [MaeC_ExSerController::class, 'consultaMes'])->name('prestarServicio.consultaMes');

Route::resource('beneficiarios', ComaeExRelParController::class)->middleware('auth')->names('exequial.beneficiarios');

Route::resource('terceros', ComaeTerController::class)->middleware('auth')->names('exequial.terceros');

Route::get('/parentescosall', [ParentescosController::class, 'index'])->name('exequial.parentescosall');

Route::get('/plansall', [PlanController::class, 'index'])->name('exequial.plansall');

//RUTAS SEGUROS
Route::resource('poliza', SegPolizaController::class)->middleware('auth');


//RUTA CARTERA MOROSOS
Route::resource('cartera', ReadExelController::class)->only(['index', 'store'])->middleware('auth')
    ->names('cartera.morosos');
Route::post('/cartera/pdfMora', [ReadExelController::class, 'pdfMora'])->middleware('auth')->name('cartera.morosos.pdfMora');
