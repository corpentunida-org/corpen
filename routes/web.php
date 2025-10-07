<?php

use App\Http\Middleware\CanDirect;
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


//ARCHIVO
use App\Http\Controllers\Archivo\GdoCargoController;
use App\Http\Controllers\Archivo\GdoAreaController;
use App\Http\Controllers\Archivo\GdoEmpleadoController;
use App\Http\Controllers\Archivo\GdoTipoDocumentoController;
use App\Http\Controllers\Archivo\GdoDocsEmpleadosController;
use App\Http\Controllers\Archivo\GdoCategoriaDocumentoController;

//MAESTARS
use App\Http\Controllers\Maestras\CongregacionController;
use App\Http\Controllers\Maestras\MaeTercerosController;
use App\Http\Controllers\Maestras\MaeTiposController;

//CREDITOS
use App\Http\Controllers\Creditos\CreditoController;

//CARTERA
use App\Http\Controllers\InteractionController;

// FLUJO
use App\Http\Controllers\Flujo\WorkflowController;
use App\Http\Controllers\Flujo\TaskController;
use App\Http\Controllers\Flujo\TaskHistoryController;
use App\Http\Controllers\Flujo\TaskCommentController;
use App\Http\Controllers\Flujo\TableroController;
use App\Models\Flujo\Workflow;
use App\Models\Flujo\Task;

// SOPORTE
use App\Http\Controllers\Soportes\ScpEstadoController;
use App\Http\Controllers\Soportes\ScpPrioridadController;
use App\Http\Controllers\Soportes\ScpTipoController;
use App\Http\Controllers\Soportes\ScpTipoObservacionController;
use App\Http\Controllers\Soportes\ScpTableroParametroController;
use App\Http\Controllers\Soportes\ScpSoporteController;
use App\Http\Controllers\Soportes\ScpObservacionController;
use App\Http\Controllers\Soportes\ScpSubTipoController;
use App\Http\Controllers\Soportes\ScpCategoriaController;
use App\Http\Controllers\Soportes\ScpUsuarioController;

//VISITAS
use App\Http\Controllers\Vistas\VisitaCorpenController;


/* Route::middleware(['auth'])->group(function () {
    Route::get('/', function () {
        //return view('');
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

Route::get('/probar-middleware-alias', function () {
    return 'Middleware con alias ejecutado ✅';
})->middleware('candirect:seguros.reclamacion.index');


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
Route::resource('convenio', SegConvenioController::class)->names('seguros.convenio')->middleware(['auth', 'candirect:seguros.convenio.index']);
Route::resource('reclamacion', SegReclamacionesController::class)->names('seguros.reclamacion')->middleware(['auth', 'candirect:seguros.reclamacion.index']);
Route::resource('novedades', SegNovedadesController::class)->names('seguros.novedades')->middleware(['auth',]);
Route::post('/reclamacion/generarpdf', [SegReclamacionesController::class, 'generarpdf'])->middleware('auth')->name('seguros.reclamacion.generarpdf');
Route::get('/reclamacion/informe/excel', [SegReclamacionesController::class, 'exportexcel'])->middleware('auth')->name('seguros.reclamacion.download');
Route::post('poliza/upload', [SegPolizaController::class, 'upload'])->name('seguros.poliza.upload');
Route::post('poliza/create/upload', [SegPolizaController::class, 'uploadCreate'])->name('seguros.poliza.createupload');
Route::get('/poliza/create/upload', function () {
    return view('seguros.polizas.upload');
})->name('seguros.poliza.viewupload');
Route::get('/seguros/cxc', [SegPolizaController::class, 'exportcxc'])->name('seguros.poliza.download');
Route::prefix('seguros')->get('/dashboard/reclamaciones', [SegReclamacionesController::class, 'dashboard'])->name('seguros.reclamaciones.dashboard');
Route::get('/planes/{edad}', [SegPlanController::class, 'getPlanes'])->name('seguros.planes.getplanes');
Route::resource('beneficios', SegBeneficiosController::class)->names('seguros.beneficios')->middleware(['auth', 'candirect:seguros.beneficios.index']);
Route::post('beneficios/list', [SegBeneficiosController::class, 'listFilter'])->name('seguros.beneficios.list');
Route::post('/seguros/filtopolizas', [SegBeneficiosController::class, 'exportFiltroPdf'])->name('seguros.poliza.filtros');
Route::post('/seguros/filtopolizas/excel', [SegBeneficiosController::class, 'exportexcel'])->middleware('auth')->name('seguros.poliza.filtroexcel');
Route::prefix('seguros')->get('/reclamacion/informe-completo', [SegReclamacionesController::class, 'exportarInformeCompleto'])->name('seguros.reclamacion.exportarInformeCompleto');


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


//TERCEROS
// ARCHIVO DE RUTAS UNIFICADO
Route::prefix('maestras')->middleware('auth')->name('maestras.')->group(function () {

    // TERCEROS
    Route::resource('terceros', MaeTercerosController::class)
        ->names('terceros')
        ->parameters(['terceros' => 'tercero']);

    Route::get('terceros/{tercero}/pdf', [MaeTercerosController::class, 'generarPdf'])
        ->name('terceros.generarPdf');

    // TIPO
    Route::resource('tipos', MaeTiposController::class)
        ->names('tipos')
        ->parameters(['tipos' => 'tipo']);

    // CONGREGACION (CORREGIDO)
    Route::resource('congregaciones', CongregacionController::class)
        ->names('congregacion')
        ->parameters(['congregaciones' => 'congregacion']);

    Route::get('buscar-pastor', [CongregacionController::class, 'buscarPastor'])
        ->name('buscar.pastor');
});
//

//CREDITOS 
Route::resource('creditos', CreditoController::class)
    ->names('creditos.credito')
    ->middleware(['auth']);

Route::prefix('creditos')->middleware('auth')->group(function () {

    /**
     * Define todas las rutas estándar (index, create, store, show, edit, update, destroy)
     * para el CreditoController, siguiendo la convención de nombres y parámetros
     * que te gusta.
     */
    Route::resource('credito', CreditoController::class)
        ->names('creditos.credito')
        ->parameters(['credito' => 'credito']);
});


// GESTION DOCUMENTAL
Route::prefix('archivo')->middleware('auth')->group(function () {

    // <-- CAMBIO CLAVE: Ruta segura para ver/descargar los manuales.
    Route::get('cargos/{cargo}/ver-manual', [GdoCargoController::class, 'verManual'])
        ->name('archivo.cargo.verManual');

    // Recursos
    Route::resource('cargos', GdoCargoController::class)
        ->names('archivo.cargo')
        ->parameters(['cargos' => 'cargo']);

    Route::resource('areas', GdoAreaController::class)
        ->names('archivo.area')
        ->parameters(['areas' => 'area']);

    Route::resource('empleados', GdoEmpleadoController::class)
        ->names('archivo.empleado')
        ->parameters(['empleados' => 'empleado']);

    Route::get('empleados/{empleado}/foto', [GdoEmpleadoController::class, 'verFoto'])
        ->name('archivo.empleado.verFoto')
        ->middleware('auth');

    Route::resource('gdotipodocumento', GdoTipoDocumentoController::class)
        ->names('archivo.gdotipodocumento')
        ->parameters(['gdotipodocumento' => 'tipoDocumento']);

    Route::resource('categorias', GdoCategoriaDocumentoController::class)
        ->names('archivo.categorias')
        ->parameters(['categorias' => 'categoria']);

    Route::resource('gdodocsempleados', GdoDocsEmpleadosController::class)
        ->names('archivo.gdodocsempleados')
        ->parameters(['gdodocsempleados' => 'gdodocsempleado']);

    Route::get('gdodocsempleados/ver/{id}', [GdoDocsEmpleadosController::class, 'verArchivo'])
        ->name('gdodocsempleados.ver')
        ->middleware('auth');

    Route::get('gdodocsempleados/download/{id}', [GdoDocsEmpleadosController::class, 'download'])
        ->name('gdodocsempleados.download')
        ->middleware('auth');
});


// --- GRUPO DE RUTAS PARA INTERACCIONES ---

Route::prefix('interactions')->middleware(['auth'])->name('interactions.')->group(function () {

    // Página principal (lista)
    Route::get('/', [InteractionController::class, 'index'])->name('index');

    // DataTables (server-side)
    Route::get('/data', [InteractionController::class, 'data'])->name('data');

    // Dashboard de estadísticas
    Route::get('/stats', [InteractionController::class, 'stats'])->name('stats');

    // Crear nueva interacción
    Route::get('/create', [InteractionController::class, 'create'])->name('create');
    Route::post('/', [InteractionController::class, 'store'])->name('store');

    // Mostrar detalle (vista completa)
    Route::get('/{interaction}/show', [InteractionController::class, 'show'])->name('show');

    // Editar / actualizar
    Route::get('/{interaction}/edit', [InteractionController::class, 'edit'])->name('edit');
    Route::put('/{interaction}', [InteractionController::class, 'update'])->name('update');

    // Eliminar
    Route::delete('/{interaction}', [InteractionController::class, 'destroy'])->name('destroy');

    // Archivos adjuntos
    Route::get('/attachment/download/{file}', [InteractionController::class, 'downloadAttachment'])->name('download');
    Route::get('/attachment/view/{fileName}', [InteractionController::class, 'viewAttachment'])->name('view');
});

// FLUJO DE TRABAJO
Route::middleware('auth')->prefix('flujo')->name('flujo.')->group(function () {
    // Workflows
    Route::resource('workflows', WorkflowController::class)
        ->names('workflows')
        ->parameters(['workflows' => 'workflow']);

    // Tasks
    Route::resource('tasks', TaskController::class)
        ->names('tasks')
        ->parameters(['tasks' => 'task']);

    // Histories (index, show, store, destroy)
    Route::resource('histories', TaskHistoryController::class)
        ->only(['index', 'show', 'store', 'destroy'])
        ->names('histories')
        ->parameters(['histories' => 'taskHistory']);

    // Comments
    Route::resource('comments', TaskCommentController::class)
        ->names('comments')
        ->parameters(['comments' => 'comment']);

    // Nuevo tablero principal
    Route::get('/tablero', [TableroController::class, 'index'])
        ->name('tablero');
});


// =============================
//   MÓDULO DE SOPORTES
// =============================
Route::middleware('auth')->prefix('soportes')->name('soportes.')->group(function () {

    // Tablero principal
    Route::get('tablero', [ScpTableroParametroController::class, 'index'])
        ->name('tablero')->middleware('candirect:soporte.lista.administrador');

    // Recursos base
    Route::resource('categorias', ScpCategoriaController::class)
        ->parameters(['categorias' => 'scpCategoria']);

    Route::resource('usuarios', ScpUsuarioController::class)
        ->parameters(['usuarios' => 'scpUsuario']);

    Route::resource('estados', ScpEstadoController::class)
        ->parameters(['estados' => 'scpEstado']);

    Route::resource('prioridades', ScpPrioridadController::class)
        ->parameters(['prioridades' => 'scpPrioridad']);

    Route::resource('tipos', ScpTipoController::class)
        ->parameters(['tipos' => 'scpTipo']);

    Route::resource('tipoObservaciones', ScpTipoObservacionController::class)
        ->parameters(['tipoObservaciones' => 'scpTipoObservacion']);

    // --- CONTROLADOR DE SOPORTES PRINCIPAL ---
    Route::resource('soportes', ScpSoporteController::class)
        ->parameters(['soportes' => 'scpSoporte']);

    // ✅ Rutas seguras para ver y descargar archivos de soporte
    Route::get('soportes/ver/{id}', [ScpSoporteController::class, 'verSoporte'])
        ->name('ver');

    Route::get('soportes/descargar/{id}', [ScpSoporteController::class, 'descargarSoporte'])
        ->name('descargar');

    // Rutas de Observaciones Anidadas
    Route::post('soportes/{scpSoporte}/observaciones', [ScpSoporteController::class, 'storeObservacion'])
        ->name('observaciones.store');

    Route::delete('soportes/{scpSoporte}/observaciones/{scpObservacion}', [ScpSoporteController::class, 'destroyObservacion'])
        ->name('observaciones.destroy');

    // Subtipos
    Route::resource('subtipos', ScpSubTipoController::class);

    // Filtros dinámicos
    Route::get('tipos/filtro/{categoria}', [ScpSoporteController::class, 'getTiposByCategoria'])
        ->name('tipos.byCategoria');

    Route::get('subtipos/filtro/{tipo}', [ScpSoporteController::class, 'getSubTipos'])
        ->name('subtipos.byTipo');

    // Vista solo de Pendientes
    Route::get('pendientes', [ScpSoporteController::class, 'pendientes'])
        ->name('pendientes');

    // Vista solo SinAsignar
    /*     Route::get('sin-asignar', [ScpSoporteController::class, 'sinAsignar'])
        ->name('soportes.sinAsignar'); */
});






//VISITAS
Route::middleware('auth')->prefix('visitas')->name('visitas.')->group(function () {

    // Buscar cliente para autocompletado
    Route::get('/cliente/buscar', [VisitaCorpenController::class, 'buscarCliente'])
        ->name('cliente.buscar');

    // Tablero general de visitas
    Route::get('tablero', [VisitaCorpenController::class, 'index'])
        ->name('tablero');

    // Visitas Corpen (Resource)
    Route::resource('corpen', VisitaCorpenController::class)
        ->names([
            'index'   => 'corpen.index',
            'create'  => 'corpen.create',
            'store'   => 'corpen.store',
            'show'    => 'corpen.show',
            'edit'    => 'corpen.edit',
            'update'  => 'corpen.update',
            'destroy' => 'corpen.destroy',
        ])
        ->parameters(['corpen' => 'visitaCorpen']);
});
