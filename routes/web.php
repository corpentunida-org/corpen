<?php

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\PermissionsController;
use App\Http\Controllers\IndexController;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuditoriaController;

use App\Http\Controllers\Cartera\ReadExelController;

use App\Http\Controllers\Exequial\ComaeTerController;
use App\Http\Controllers\Exequial\ComaeExCliController;
use App\Http\Controllers\Exequial\ComaeExRelParController;
use App\Http\Controllers\Exequial\ParentescosController;
use App\Http\Controllers\Exequial\MaeC_ExSerController;
use App\Http\Controllers\Exequial\PlanController;

use App\Http\Controllers\Seguros\SegPolizaController;
use App\Http\Controllers\Seguros\SegPlanController;
use App\Http\Controllers\Seguros\SegBeneficiarioController;
use App\Http\Controllers\Seguros\SegCoberturaController;
use App\Http\Controllers\Seguros\SegConvenioController;
use App\Http\Controllers\Seguros\SegReclamacionesController;
use App\Http\Controllers\Seguros\SegNovedadesController;
use App\Http\Controllers\Seguros\SegBeneficiosController;

use App\Http\Controllers\Cinco\TercerosController;
use App\Http\Controllers\Cinco\MoviContCincoController;
use App\Http\Controllers\Cinco\RetirosListadoController;
use App\Http\Controllers\Cinco\CondicionesRetirosController;

use App\Http\Controllers\ResReservaController;

use App\Http\Controllers\Maestras\CongregacionController;

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

Route::get('/apto-santamarta', function () {
    return view('reserva.index');
})->name('apto-santamarta');



//ADMIN
Route::resource('users', UserController::class)->names('admin.users')->middleware(['auth', 'can:admin.users.index']);
Route::resource('admin', AuditoriaController::class)->names('admin.auditoria')->middleware(['auth', 'can:admin.auditoria.index']);
Route::resource('roles', RoleController::class)->names('admin.roles')->middleware(['auth']);
Route::resource('permisos', PermissionsController::class)->names('admin.permisos')->middleware(['auth']);

//RUTAS DE EXEQUIALES
Route::get('asociados/{id}/generarpdf/{active}', [ComaeExCliController::class, 'generarpdf'])->name('asociados.generarpdf');
Route::resource('asociados', ComaeExCliController::class)->names('exequial.asociados')->middleware(['auth', 'can:exequial.asociados.index']);
Route::get('/prestarServicio/generarpdf', [MaeC_ExSerController::class, 'generarpdf'])->middleware('auth')->name('prestarServicio.generarpdf');
Route::resource('prestarServicio', MaeC_ExSerController::class)->names('exequial.prestarServicio')->middleware(['auth', 'can:exequial.prestarServicio.index']);
Route::get('/prestarServicio/{id}/generarpdf', [MaeC_ExSerController::class, 'reporteIndividual'])->name('prestarServicio.repIndividual');
Route::get('/exportar-datos', [MaeC_ExSerController::class, 'exportData']);
Route::resource('beneficiarios', ComaeExRelParController::class)->middleware('auth')->names('exequial.beneficiarios');
Route::resource('terceros', ComaeTerController::class)->middleware('auth')->names('exequial.terceros');
Route::get('/parentescosall', [ParentescosController::class, 'index'])->name('exequial.parentescosall');
Route::get('/plansall', [PlanController::class, 'index'])->name('exequial.plansall');

//RUTAS SEGUROS
Route::get('/poliza/formato', [SegPolizaController::class, 'exportarFormato'])->name('seguros.poliza.formato');
Route::resource('poliza', SegPolizaController::class)->names('seguros.poliza')->middleware(['auth', 'can:seguros.poliza.index']);
Route::get('/polizaname/{name}', [SegPolizaController::class, 'namesearch'])->name('poliza.search');
Route::resource('plan', SegPlanController::class)->names('seguros.planes')->middleware('auth');
Route::resource('cobertura', SegCoberturaController::class)->names('seguros.cobertura')->middleware('auth');
Route::resource('beneficiario', SegBeneficiarioController::class)->names('seguros.beneficiario')->middleware('auth');
Route::resource('convenio', SegConvenioController::class)->names('seguros.convenio')->middleware(['auth', 'can:seguros.convenio.index']);
Route::resource('reclamacion', SegReclamacionesController::class)->names('seguros.reclamacion')->middleware(['auth', 'can:seguros.reclamacion.index']);
Route::resource('novedades', SegNovedadesController::class)->names('seguros.novedades')->middleware(['auth',]);
Route::post('/reclamacion/generarpdf', [SegReclamacionesController::class, 'generarpdf'])->middleware('auth')->name('seguros.reclamacion.generarpdf');
Route::get('/reclamacion/informe/excel', [SegReclamacionesController::class, 'exportexcel'])->middleware('auth')->name('seguros.reclamacion.download');
Route::post('poliza/upload', [SegPolizaController::class, 'upload'])->name('seguros.poliza.upload');
Route::post('poliza/create/upload', [SegPolizaController::class, 'uploadCreate'])->name('seguros.poliza.createupload');
Route::get('/poliza/create/upload', function () {return view('seguros.polizas.upload');})->name('seguros.poliza.viewupload');
Route::get('/seguros/cxc', [SegPolizaController::class, 'exportcxc'])->name('seguros.poliza.download');
Route::get('/planes/{edad}', [SegPlanController::class, 'getPlanes'])->name('seguros.planes.getplanes');
Route::resource('beneficios', SegBeneficiosController::class)->names('seguros.beneficios')->middleware(['auth',]);
Route::post('beneficios/list', [SegBeneficiosController::class, 'listFilter'])->name('seguros.beneficios.list');

//RUTAS CINCO
Route::resource('terceros', TercerosController::class)->names('cinco.tercero')->middleware(['auth', 'can:cinco.tercero.index']);
Route::resource('cinco', MoviContCincoController::class)->names('cinco.movcontables')->middleware(['auth', 'can:cinco.movcontables.index']);
Route::resource('calculoretiros', RetirosListadoController::class)->names('cinco.retiros')->middleware(['auth', 'can:cinco.retiros.index']);
Route::resource('condicionRetiros', CondicionesRetirosController::class)->names('cinco.condicionRetiros')->middleware(['auth', 'can:cinco.retiros.index']);
Route::get('condicionRetiros/{id}/reportepdf/', [CondicionesRetirosController::class, 'generarpdf'])->name('cinco.liquidacionretiro');
Route::get('movcontables/{id}/reportepdf/', [MoviContCincoController::class, 'generarpdf'])->name('cinco.reportepdf');
Route::get('/retirosname/{name}', [RetirosListadoController::class, 'namesearch'])->name('cinco.retiros.search');

//RUTA CARTERA MOROSOS
Route::get('cartera', [ReadExelController::class, 'index'])
    ->name('cartera.morosos.index')->middleware(['auth', 'can:cartera.listamorosos.generarcarta']);
Route::post('cartera', [ReadExelController::class, 'store'])
    ->name('cartera.morosos.store')->middleware(['auth', 'can:cartera.listamorosos.generarcarta']);
Route::post('/cartera/pdfMora', [ReadExelController::class, 'pdfMora'])->middleware('auth')->name('cartera.morosos.pdfMora');

//Módulo inventario

Route::get('/inventario', [UserController::class, 'inventario'])->middleware('auth')->name('inventario');
//Route::get('/inventario/{id}', [UserController::class, 'inventario'])->middleware('auth')->name('inventario');

Route::get('user/validation/asociado', [UserController::class, 'validarAsociadoCreate'])->name('user.validar.asociado');
Route::get('/consumir-api', [UserController::class, 'consumirEndpoint']);
Route::post('validar/asociado', [UserController::class, 'validarAsociado'])->name('validar.asociado');

Route::resource('reserva', ResReservaController::class)->names('reserva');
Route::get('reservaI/{id}/create', [ResReservaController::class, 'createReserva'])->name('reserva.inmueble.create');
Route::post('reservaI/store', [ResReservaController::class, 'storeReserva'])->name('reserva.inmueble.store');
Route::get('reservaI/{id}/soporte', [ResReservaController::class, 'createSoporte'])->name('reserva.inmueble.soporte.create');
Route::post('reservaI/storeSoporte', [ResReservaController::class, 'storeSoporte'])->name('reserva.inmueble.soporte.store');

Route::get('reservaI/confirmacion', [ResReservaController::class, 'indexConfirmacion'])->name('reserva.inmueble.confirmacion');
Route::get('reservaI/confirmacion/{id}/show', [ResReservaController::class, 'showConfirmacion'])->name('reserva.inmueble.confirmacion.show');
Route::post('reservaI/notificar/ajuste', [ResReservaController::class, 'notificarAjuste'])->name('reserva.inmueble.notificar.ajuste');
Route::post('reservaI/confirmar', [ResReservaController::class, 'confirmar'])->name('reserva.inmueble.confirmar');

Route::get('reservaI/historico', [ResReservaController::class, 'indexHistorico'])->name('reserva.inmueble.historico');

//MAESTRAS
Route::resource('maestras', CongregacionController::class)->names('maestras.congregacion')->middleware(['auth']);