<?php


use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Cartera\ReadExelController;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\Seguros\SegBeneficiarioController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuditoriaController;

use App\Http\Controllers\Exequial\ComaeTerController;
use App\Http\Controllers\Exequial\ComaeExCliController;
use App\Http\Controllers\Exequial\ComaeExRelParController;
use App\Http\Controllers\Exequial\ParentescosController;
use App\Http\Controllers\Exequial\MaeC_ExSerController;
use App\Http\Controllers\Exequial\PlanController;

use App\Http\Controllers\Seguros\SegPolizaController;
use App\Http\Controllers\Seguros\SegPlanController;


/* Route::middleware(['auth'])->group(function () {
    Route::get('/', function () {
        //return view('exequial.asociados.index');
    })->name('dashboard');
}); */
Route::middleware(['auth'])->group(function () {
    Route::get('/', [IndexController::class, 'index'])->name('dashboard');
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
Route::resource('admin', AuditoriaController::class)->middleware('auth')->names('admin.auditoria');


//RUTAS DE EXEQUIALES
Route::get('asociados/{id}/generarpdf/{active}', [ComaeExCliController::class, 'generarpdf'])->name('asociados.generarpdf');
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
Route::resource('poliza', SegPolizaController::class)->middleware('auth')->names('seguros.poliza');
Route::resource('plan', SegPlanController::class)->middleware('auth')->names('seguros.planes');
Route::resource('beneficiario', SegBeneficiarioController::class)->middleware('auth')->names('seguros.beneficiario');


//RUTA CARTERA MOROSOS
Route::resource('cartera', ReadExelController::class)->only(['index', 'store'])->middleware('auth')
    ->names('cartera.morosos');
Route::post('/cartera/pdfMora', [ReadExelController::class, 'pdfMora'])->middleware('auth')->name('cartera.morosos.pdfMora');