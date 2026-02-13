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
use App\Http\Controllers\Maestras\MaeMunicipiosController;

//CREDITOS
use App\Http\Controllers\Creditos\CreditoController;

//INTERACCIONES
use App\Models\Maestras\maeTerceros;
use App\Http\Controllers\Interacciones\InteractionController;
use App\Http\Controllers\Interacciones\IntChannelController;
use App\Http\Controllers\Interacciones\IntTypeController;
use App\Http\Controllers\Interacciones\IntOutcomeController;
use App\Http\Controllers\Interacciones\IntNextActionController;

// FLUJO
use App\Http\Controllers\Flujo\WorkflowController;
use App\Http\Controllers\Flujo\TaskController;
use App\Http\Controllers\Flujo\TaskHistoryController;
use App\Http\Controllers\Flujo\TaskCommentController;
use App\Http\Controllers\Flujo\TableroController;
use App\Http\Controllers\Flujo\AuditoriaProyectosController;
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
use App\Http\Controllers\Soportes\ScpNotificacionController;
use App\Http\Controllers\Soportes\ScpEstadisticaController;

//INVENTARIO
use App\Http\Controllers\Inventario\TableroInventarioController; 
use App\Http\Controllers\Inventario\ActivoController;
use App\Http\Controllers\Inventario\CompraController;
use App\Http\Controllers\Inventario\MovimientoController;
use App\Http\Controllers\Inventario\MantenimientoController;
use App\Http\Controllers\Inventario\MarcaController;
use App\Http\Controllers\Inventario\BodegaController;
use App\Http\Controllers\Inventario\EstadoController;
use App\Http\Controllers\Inventario\MetodoPagoController;
use App\Http\Controllers\Inventario\ClasificacionController;

//VISITAS
use App\Http\Controllers\Vistas\VisitaCorpenController;

//QUIZ INDICATORS
use App\Http\Controllers\Indicators\QuizController;
use App\Http\Controllers\Indicators\IndicadoresController;


/* Route::middleware(['auth'])->group(function () {
    Route::get('/', function () {
        //return view('');
    })->name('dashboard');
}); */


//CORRESPONDENCIA
use App\Http\Controllers\Correspondencia\TableroCorrespondenciaController;
use App\Http\Controllers\Correspondencia\CorrespondenciaController;
use App\Http\Controllers\Correspondencia\FlujoDeTrabajoController;
use App\Http\Controllers\Correspondencia\ProcesoController;
use App\Http\Controllers\Correspondencia\NotificacionController;
use App\Http\Controllers\Correspondencia\TrdController;
use App\Http\Controllers\Correspondencia\PlantillaController;
use App\Http\Controllers\Correspondencia\ComunicacionSalidaController;
use App\Http\Controllers\Correspondencia\CorrespondenciaProcesoController;
use App\Http\Controllers\Correspondencia\CorrEstadoController;
use App\Http\Controllers\Correspondencia\MedioRecepcionController;

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
Route::resource('admin', AuditoriaController::class)->names('admin.auditoria')->middleware(['auth', 'candirect:admin.auditoria.index']);
Route::resource('roles', RoleController::class)->names('admin.roles')->middleware(['auth']);
Route::resource('permisos', PermissionsController::class)->names('admin.permisos')->middleware(['auth']);

//RUTAS DE EXEQUIALES
Route::prefix('exequiales')->group(function () {
    Route::get('asociados/{id}/generarpdf/{active}', [ComaeExCliController::class, 'generarpdf'])->name('asociados.generarpdf');
    Route::resource('asociados', ComaeExCliController::class)->names('exequial.asociados')->middleware(['auth', 'can:exequial.asociados.index']);
    Route::get('/prestarServicio/generarpdf', [MaeC_ExSerController::class, 'generarpdf'])->middleware('auth')->name('prestarServicio.generarpdf');
    Route::get('prestarServicio/dashboard', [MaeC_ExSerController::class, 'dashboard'])->name('exequial.prestarServicio.dashboard');
    Route::get('prestarServicio/excel', [MaeC_ExSerController::class, 'generarExcelPrestarServicio'])->name('prestarServicio.generate.excel');
    Route::resource('prestarServicio', MaeC_ExSerController::class)->names('exequial.prestarServicio')->middleware(['auth', 'can:exequial.prestarServicio.index']);
    Route::get('/prestarServicio/{id}/generarpdf', [MaeC_ExSerController::class, 'reporteIndividual'])->name('prestarServicio.repIndividual');
    Route::get('/exportar-datos', [MaeC_ExSerController::class, 'exportData']);
    Route::resource('beneficiarios', ComaeExRelParController::class)->middleware('auth')->names('exequial.beneficiarios');
    Route::resource('terceros', ComaeTerController::class)->middleware('auth')->names('exequial.terceros');
    Route::get('/parentescosall', [ParentescosController::class, 'index'])->name('exequial.parentescosall');
    Route::get('/plansall', [PlanController::class, 'index'])->name('exequial.plansall');
    Route::post('/prestarServicio/{id}/comentario', [MaeC_ExSerController::class, 'addComment'])->name('prestarServicio.comentario.store');
});

//RUTAS SEGUROS
Route::prefix('seguros')->group(function () {
    Route::get('/poliza/formato', [SegPolizaController::class, 'exportarFormato'])->name('seguros.poliza.formato');
    Route::resource('poliza', SegPolizaController::class)->names('seguros.poliza')->middleware(['auth', 'candirect:seguros.poliza.index']);
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
    Route::get('/poliza/create/upload', function () {return view('seguros.polizas.upload');})->name('seguros.poliza.viewupload');
    Route::get('/seguros/cxc', [SegPolizaController::class, 'exportcxc'])->name('seguros.poliza.download');
    Route::get('/dashboard/reclamaciones', [SegReclamacionesController::class, 'dashboard'])->name('seguros.reclamaciones.dashboard');
    Route::get('/planes/{edad}', [SegPlanController::class, 'getPlanes'])->name('seguros.planes.getplanes');
    Route::resource('beneficios', SegBeneficiosController::class)->names('seguros.beneficios')->middleware(['auth', 'candirect:seguros.beneficios.index']);
    Route::post('beneficios/list', [SegBeneficiosController::class, 'listFilter'])->name('seguros.beneficios.list');
    Route::post('/seguros/filtopolizas', [SegBeneficiosController::class, 'exportFiltroPdf'])->name('seguros.poliza.filtros');
    Route::post('/seguros/filtopolizas/excel', [SegBeneficiosController::class, 'exportexcel'])->middleware('auth')->name('seguros.poliza.filtroexcel');
    Route::prefix('seguros')->get('/reclamacion/informe-completo', [SegReclamacionesController::class, 'exportarInformeCompleto'])->name('seguros.reclamacion.exportarInformeCompleto');
    Route::get('/novedades/{id}/formulario', [SegNovedadesController::class, 'verArchivo'])->name('seguros.novedades.formulario');
    Route::get('/seguros/novedades/download', [SegNovedadesController::class, 'descargarexcel'])->name('seguros.novedades.download');
});

//RUTAS CINCO
Route::prefix('cinco')->group(function () {
    Route::resource('terceros', TercerosController::class)->names('cinco.tercero')->middleware(['auth', 'can:cinco.tercero.index']);
    Route::resource('cinco', MoviContCincoController::class)->names('cinco.movcontables')->middleware(['auth', 'can:cinco.movcontables.index']);
    Route::get('movcontables/{id}/reportepdf/', [MoviContCincoController::class, 'generarpdf'])->name('cinco.reportepdf');
});
Route::prefix('retiros')->group(function () {
    Route::resource('calculoretiros', RetirosListadoController::class)->names('cinco.retiros')->middleware(['auth', 'can:cinco.retiros.index']);
    Route::resource('condicionRetiros', CondicionesRetirosController::class)->names('cinco.condicionRetiros')->middleware(['auth', 'can:cinco.retiros.index']);
    Route::get('/retirosname/{name}', [RetirosListadoController::class, 'namesearch'])->name('cinco.retiros.search');
    Route::get('condicionRetiros/{id}/reportepdf/', [CondicionesRetirosController::class, 'generarpdf'])->name('cinco.liquidacionretiro');
});

//RUTA CARTERA MOROSOS
Route::get('cartera', [ReadExelController::class, 'index'])->name('cartera.morosos.index')->middleware(['auth', 'can:cartera.listamorosos.generarcarta']);
Route::post('cartera', [ReadExelController::class, 'store'])
    ->name('cartera.morosos.store')->middleware(['auth', 'can:cartera.listamorosos.generarcarta']);
Route::post('/cartera/pdfMora', [ReadExelController::class, 'pdfMora'])->middleware('auth')->name('cartera.morosos.pdfMora');

//Módulo inventario
Route::get('/inventario', [UserController::class, 'inventario'])->middleware('auth')->name('inventario');

//RESERVAS
Route::get('user/validation/asociado', [UserController::class, 'validarAsociadoCreate'])->name('user.validar.asociado');
Route::get('/consumir-api', [UserController::class, 'consumirEndpoint']);
Route::post('validar/asociado', [UserController::class, 'validarAsociado'])->name('validar.asociado');

Route::resource('reserva', ResReservaController::class)->names('reserva');
Route::get('reservaI/{id}/create', [ResReservaController::class, 'createReserva'])->name('reserva.inmueble.create');
Route::post('reservaI/store', [ResReservaController::class, 'storeReserva'])->name('reserva.inmueble.store');
Route::get('reservaI/{id}/soporte', [ResReservaController::class, 'createSoporte'])->name('reserva.inmueble.soporte.create');
Route::post('reservaI/storeSoporte', [ResReservaController::class, 'storeSoporte'])->name('reserva.inmueble.soporte.store');

Route::get('reservaI/confirmacion', [ResReservaController::class, 'indexConfirmacion'])->name('reserva.inmueble.confirmacion')->middleware(['auth', 'candirect:reservas.Reserva.lista']);
Route::get('reservaI/confirmacion/{id}/show', [ResReservaController::class, 'showConfirmacion'])->name('reserva.inmueble.confirmacion.show')->middleware(['auth', 'candirect:reservas.Reserva.lista']);
Route::post('reservaI/notificar/ajuste', [ResReservaController::class, 'notificarAjuste'])->name('reserva.inmueble.notificar.ajuste');
Route::post('reservaI/confirmar', [ResReservaController::class, 'confirmar'])->name('reserva.inmueble.confirmar');

Route::get('reservaI/historico', [ResReservaController::class, 'indexHistorico'])->name('reserva.inmueble.historico')->middleware(['auth', 'candirect:reservas.Reserva.historico']);


//TERCEROS
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

    Route::get('departamentos/{region}', [MaeMunicipiosController::class, 'listadepartamentos'])->name('departamentos.listar');
    Route::get('municipios/{departamento}', [MaeMunicipiosController::class, 'listamunicipios'])->name('municipios.listar');
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


// =========================================================================
// MÓDULO DE GESTIÓN DOCUMENTAL (AWS S3 OPTIMIZED)
// =========================================================================
    Route::prefix('archivo')->middleware(['auth'])->group(function () {

        // --- GESTIÓN DE CARGOS Y ÁREAS ---
        
        // Exportación de datos (Se coloca antes del resource)
        Route::get('cargos/export-csv', [GdoCargoController::class, 'exportCsv'])
            ->name('archivo.cargo.export.csv');

        // Ruta segura para ver manuales de funciones desde S3
        Route::get('cargos/{cargo}/ver-manual', [GdoCargoController::class, 'verManual'])
            ->name('archivo.cargo.verManual');

        Route::resource('cargos', GdoCargoController::class)
            ->names('archivo.cargo')
            ->parameters(['cargos' => 'cargo']);

        Route::resource('areas', GdoAreaController::class)
            ->names('archivo.area')
            ->parameters(['areas' => 'area']);


        // --- GESTIÓN DE EMPLEADOS (Controlador Unificado) ---
        // Rutas para el CRUD de empleados
        Route::get('empleados', [GdoEmpleadoController::class, 'index'])
            ->name('archivo.empleado.index');
        
        Route::post('empleados', [GdoEmpleadoController::class, 'store'])
            ->name('archivo.empleado.store');
        
        Route::put('empleados/{empleado}', [GdoEmpleadoController::class, 'update'])
            ->name('archivo.empleado.update');
        
        Route::delete('empleados/{empleado}', [GdoEmpleadoController::class, 'destroy'])
            ->name('archivo.empleado.destroy');

        // Visualización de Foto de Perfil (URL Firmada S3)
        Route::get('empleados/{id}/foto', [GdoEmpleadoController::class, 'verFoto'])
            ->name('archivo.empleado.verFoto');


        // --- GESTIÓN DE DOCUMENTOS ADICIONALES (Implementación S3) ---
        // Subida de documentos adicionales
        Route::post('empleados/storeDocumento', [GdoEmpleadoController::class, 'storeDocumento'])
            ->name('archivo.empleado.storeDocumento');

        // Rutas para ver y descargar documentos (ahora en GdoEmpleadoController)
        Route::get('empleados/verDocumento/{id}', [GdoEmpleadoController::class, 'verDocumento'])
            ->name('archivo.empleado.verDocumento');

        Route::get('empleados/downloadDocumento/{id}', [GdoEmpleadoController::class, 'downloadDocumento'])
            ->name('archivo.empleado.downloadDocumento');

        // Eliminación física y lógica de documentos adicionales
        Route::delete('empleados/documentos/{id}', [GdoEmpleadoController::class, 'destroyDocumento'])
            ->name('archivo.empleado.destroyDocumento');
        
        // Ruta para el menú de navegación (apunta al mismo método index)
        Route::get('gdodocsempleados', [GdoEmpleadoController::class, 'index'])
            ->name('archivo.gdodocsempleados.index');


        // --- CONFIGURACIONES MAESTRAS ---
        Route::resource('gdotipodocumento', GdoTipoDocumentoController::class)
            ->names('archivo.gdotipodocumento')
            ->parameters(['gdotipodocumento' => 'tipoDocumento']);

        Route::resource('categorias', GdoCategoriaDocumentoController::class)
            ->names('archivo.categorias')
            ->parameters(['categorias' => 'categoria']);
        
        // --- GESTIÓN DE FUNCIONES (ACTUALIZADO) ---
        
        // CRUD Principal de Funciones
        Route::resource('funciones', \App\Http\Controllers\Archivo\GdoFuncionController::class)
            ->names('archivo.funcion')
            ->parameters(['funciones' => 'funcion']);

        // Ruta para asignar una función a un cargo (Crear o Actualizar Pivot)
        Route::post('funciones/asignar-cargo', [\App\Http\Controllers\Archivo\GdoFuncionController::class, 'asignarCargo'])
            ->name('archivo.funcion.asignarCargo');
            
        // NUEVA RUTA: Cambiar estado Activo/Inactivo (Reemplaza a desvincular)
        Route::put('funciones/{cargo}/{funcion}/estado', [\App\Http\Controllers\Archivo\GdoFuncionController::class, 'cambiarEstadoVinculo'])
            ->name('archivo.funcion.cambiarEstadoVinculo');
            
    });
// FIN DE GESTIÓN DOCUMENTAL

// =============================
//   MÓDULO DE INTERACCIONES
// =============================
    Route::prefix('interactions')
        ->middleware(['auth'])
        ->name('interactions.')
        ->group(function () {

        // 📄 Página principal (lista de interacciones)
        Route::get('/', [InteractionController::class, 'index'])->name('index');

        // ➕ Crear nueva interacción
        Route::get('/create', [InteractionController::class, 'create'])->name('create');
        Route::post('/', [InteractionController::class, 'store'])->name('store');

        // 👁️ Mostrar detalle
        Route::get('/{interaction}/show', [InteractionController::class, 'show'])->name('show');

        // ✏️ Editar / actualizar
        Route::get('/{interaction}/edit', [InteractionController::class, 'edit'])->name('edit');
        Route::put('/{interaction}', [InteractionController::class, 'update'])->name('update');

        // 🗑️ Eliminar
        Route::delete('/{interaction}', [InteractionController::class, 'destroy'])->name('destroy');

        // 📎 Archivos adjuntos
        Route::get('/attachment/download/{fileName}', [InteractionController::class, 'downloadAttachment'])->name('download');
        Route::get('/attachment/view/{fileName}', [InteractionController::class, 'viewAttachment'])->name('view');

        // 📌 AJAX: Obtener datos del cliente por cod_ter
        Route::get('/cliente/{cod_ter}', [InteractionController::class, 'getCliente'])->name('cliente.show');
        
        // 📌 AJAX: Buscar clientes para Select2
        Route::get('/search-clients', [InteractionController::class, 'searchClients'])->name('search-clients');
        // 🆕 NUEVA RUTA: Obtener el distrito de un cliente
        Route::get('/clientes/{client_id}/distrito', [InteractionController::class, 'getClientDistrict'])->name('clientes.distrito');
        // 🆕 NUEVA RUTA: Actualizar el distrito de un cliente
        Route::put('/clientes/{client_id}/actualizar-distrito', [InteractionController::class, 'updateClientDistrict'])->name('clientes.actualizar-distrito');
        // --- 📡 GRUPO DE RUTAS PARA CANALES DE INTERACCIÓN ---
        Route::prefix('channels')->name('channels.')->group(function () {
            Route::get('/', [IntChannelController::class, 'index'])->name('index');
            Route::get('/create', [IntChannelController::class, 'create'])->name('create');
            Route::post('/', [IntChannelController::class, 'store'])->name('store');
            Route::get('/{channel}', [IntChannelController::class, 'show'])->name('show');
            Route::get('/{channel}/edit', [IntChannelController::class, 'edit'])->name('edit');
            Route::put('/{channel}', [IntChannelController::class, 'update'])->name('update');
            Route::delete('/{channel}', [IntChannelController::class, 'destroy'])->name('destroy');
        });

        // --- 📡 GRUPO DE RUTAS PARA TIPOS DE INTERACCIÓN ---
        Route::prefix('types')->name('types.')->group(function () {
            Route::get('/', [IntTypeController::class, 'index'])->name('index');
            Route::get('/create', [IntTypeController::class, 'create'])->name('create');
            Route::post('/', [IntTypeController::class, 'store'])->name('store');
            Route::get('/{type}', [IntTypeController::class, 'show'])->name('show');
            Route::get('/{type}/edit', [IntTypeController::class, 'edit'])->name('edit');
            Route::put('/{type}', [IntTypeController::class, 'update'])->name('update');
            Route::delete('/{type}', [IntTypeController::class, 'destroy'])->name('destroy');
        });

        // --- 📡 GRUPO DE RUTAS PARA RESULTADOS DE INTERACCIÓN ---
        Route::prefix('outcomes')->name('outcomes.')->group(function () {
            Route::get('/', [IntOutcomeController::class, 'index'])->name('index');
            Route::get('/create', [IntOutcomeController::class, 'create'])->name('create');
            Route::post('/', [IntOutcomeController::class, 'store'])->name('store');
            Route::get('/{outcome}', [IntOutcomeController::class, 'show'])->name('show');
            Route::get('/{outcome}/edit', [IntOutcomeController::class, 'edit'])->name('edit');
            Route::put('/{outcome}', [IntOutcomeController::class, 'update'])->name('update');
            Route::delete('/{outcome}', [IntOutcomeController::class, 'destroy'])->name('destroy');
        });


        // --- 📡 GRUPO DE RUTAS PARA PRÓXIMAS ACCIONES ---
        Route::prefix('next_actions')->name('next_actions.')->group(function () {
            Route::get('/', [IntNextActionController::class, 'index'])->name('index');
            Route::get('/create', [IntNextActionController::class, 'create'])->name('create');
            Route::post('/', [IntNextActionController::class, 'store'])->name('store');
            Route::get('/{action}', [IntNextActionController::class, 'show'])->name('show');
            Route::get('/{action}/edit', [IntNextActionController::class, 'edit'])->name('edit');
            Route::put('/{action}', [IntNextActionController::class, 'update'])->name('update');
            Route::delete('/{action}', [IntNextActionController::class, 'destroy'])->name('destroy');
        });
    });
//FIN INTERACCIONES

// =============================
//   MÓDULO DE PROYECTOS
// =============================
    Route::middleware('auth')->prefix('flujo')->name('flujo.')->group(function () {
        
        // Rutas personalizadas de Workflows
        // 1. Generar PDF
        Route::get('workflows/{workflow}/pdf', [WorkflowController::class, 'generatePdf'])
            ->name('workflows.pdf');
            
        // 2. ACTUALIZAR EQUIPO (AJAX) - ESTA ES LA RUTA NUEVA
        Route::put('workflows/{workflow}/update-team', [WorkflowController::class, 'updateTeam'])
            ->name('workflows.updateTeam');

        // Workflows (Resource estándar)
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

        // Auditoría
        Route::get('/auditoria/pdf', [AuditoriaProyectosController::class, 'exportPdf'])
            ->name('auditoria.pdf'); 

        Route::get('/auditoria', [AuditoriaProyectosController::class, 'index'])
            ->name('auditoria.index');
            
        // Nuevo tablero principal
        Route::get('/tablero', [TableroController::class, 'index'])
            ->name('tablero');
    });
// FIN PROYECTOS

// =============================
//   MÓDULO DE SOPORTES
// =============================
    Route::middleware('auth')->prefix('soportes')->name('soportes.')->group(function () {

        // ---------------------------------------------------
        // 1. DASHBOARD Y ESTADÍSTICAS
        // ---------------------------------------------------
        
        // Tablero principal
        Route::get('tablero', [ScpTableroParametroController::class, 'index'])
            ->name('tablero')
            ->middleware('candirect:soporte.lista.administrador');
        
        // Vista de Estadísticas
        Route::get('estadisticas', [ScpEstadisticaController::class, 'index'])
            ->name('estadisticas')
            ->middleware('candirect:soporte.lista.administrador');
        
        // API AJAX para los gráficos (Sin middleware restrictivo para evitar bloqueos en fetch)
        Route::get('estadisticas/data', [ScpEstadisticaController::class, 'getDashboardData'])
            ->name('estadisticas.data');


        // ---------------------------------------------------
        // 2. CONFIGURACIÓN Y PARAMÉTRICAS (RESOURCES)
        // ---------------------------------------------------
        Route::resource('categorias', ScpCategoriaController::class)
            ->parameters(['categorias' => 'scpCategoria']);

        Route::resource('estados', ScpEstadoController::class)
            ->parameters(['estados' => 'scpEstado']);

        Route::resource('prioridades', ScpPrioridadController::class)
            ->parameters(['prioridades' => 'scpPrioridad']);

        Route::resource('tipos', ScpTipoController::class)
            ->parameters(['tipos' => 'scpTipo']);

        Route::resource('subtipos', ScpSubTipoController::class)
            ->parameters(['subtipos' => 'scpSubtipo']); 

        Route::resource('tipoObservaciones', ScpTipoObservacionController::class)
            ->parameters(['tipoObservaciones' => 'scpTipoObservacion']);


        // ---------------------------------------------------
        // 3. GESTIÓN DE USUARIOS (AGENTES)
        // ---------------------------------------------------
        // Rutas con Hash (Edit/Update) - IMPORTANTE: Deben ir antes del resource estándar
        Route::get('usuarios/{hash}/edit', [ScpUsuarioController::class, 'edit'])
            ->name('usuarios.edit');

        Route::put('usuarios/{hash}', [ScpUsuarioController::class, 'update'])
            ->name('usuarios.update');

        // CRUD Base de usuarios (Excluyendo lo que manejan las rutas Hash)
        Route::resource('usuarios', ScpUsuarioController::class)
            ->parameters(['usuarios' => 'scpUsuario'])
            ->except(['edit', 'update']);


        // ---------------------------------------------------
        // 4. GESTIÓN PRINCIPAL DE SOPORTES
        // ---------------------------------------------------

        // --- Rutas Específicas (Deben ir ANTES del Resource 'soportes' para evitar conflictos) ---

        // 🔍 Buscador Rápido (Autocompletado JS)
        Route::get('buscar-rapido', [ScpSoporteController::class, 'quickSearch'])
            ->name('buscar.rapido');

        // 📂 Vistas filtradas predefinidas
        Route::get('pendientes', [ScpSoporteController::class, 'pendientes'])
            ->name('pendientes');

        Route::get('mis-soportes', [ScpSoporteController::class, 'misSoportes']) // Ver tickets asignados a mí
            ->name('mis-soportes');

        Route::get('sin-asignar', [ScpSoporteController::class, 'sinAsignar'])
            ->name('sinAsignar');

        // ⚙️ Acciones sobre el soporte
        Route::get('descargar/{id}', [ScpSoporteController::class, 'descargarSoporte'])
            ->name('descargar');
            
        Route::get('ver/{id}', [ScpSoporteController::class, 'verSoporte'])
            ->name('ver');
        
        // Asignar agente y Cambiar Estado (Acciones específicas)
        Route::post('{scpSoporte}/asignar', [ScpSoporteController::class, 'asignarAgente'])
            ->name('asignar');
            
        Route::post('{scpSoporte}/cambiar-estado', [ScpSoporteController::class, 'cambiarEstado'])
            ->name('cambiarEstado');

        // --- Resource Principal (CRUD Completo) ---
        Route::resource('soportes', ScpSoporteController::class)
            ->parameters(['soportes' => 'scpSoporte']);


        // ---------------------------------------------------
        // 5. OBSERVACIONES Y ADJUNTOS
        // ---------------------------------------------------
        Route::post('soportes/{scpSoporte}/observaciones', [ScpSoporteController::class, 'storeObservacion'])
            ->name('observaciones.store');

    Route::delete('soportes/{scpSoporte}/observaciones/{scpObservacion}', [ScpSoporteController::class, 'destroyObservacion'])
        ->name('observaciones.destroy');

        // Descarga de adjuntos de observaciones
        Route::get('observaciones/adjunto/{id}', [ScpSoporteController::class, 'descargarAdjuntoObservacion'])
            ->name('observaciones.adjunto');


        // ---------------------------------------------------
        // 6. FILTROS DINÁMICOS (AJAX SELECTS)
        // ---------------------------------------------------
        Route::get('tipos/filtro/{categoria}', [ScpSoporteController::class, 'getTiposByCategoria'])
            ->name('tipos.byCategoria');

    // ---------------------------------------------------
    // 6. FILTROS DINÁMICOS (AJAX SELECTS)
    // ---------------------------------------------------
    Route::get('tipos/filtro/{categoria}', [ScpSoporteController::class, 'getTiposByCategoria'])
        ->name('tipos.byCategoria');

        Route::get('subtipos/filtro/{tipo}', [ScpSoporteController::class, 'getSubTipos'])
            ->name('subtipos.byTipo');

        // ---------------------------------------------------
        // 7. NOTIFICACIONES Y CORREOS
        // ---------------------------------------------------
        // Notificaciones (JSON para la campana del navbar)
        Route::get('notificaciones', [ScpSoporteController::class, 'getNotificaciones'])
            ->name('notificaciones');

        // Vista completa de notificaciones
        Route::get('notificaciones/detalladas', [ScpSoporteController::class, 'getNotificacionesDetalladas'])->name('notificaciones.detalladas')->middleware('auth');

        // Acción manual de reenvío de correo
        Route::get('enviar-correo-escalado/{id}', [ScpNotificacionController::class, 'enviarCorreoEscalado'])
            ->name('enviarCorreoEscalado');

    });
//FIN SOPORTE

// ==========================================
//   MÓDULO DE INVENTARIOS
// ==========================================
    Route::middleware(['auth'])->prefix('inventario')->name('inventario.')->group(function () {

        // ---------------------------------------------------
        // 1. DASHBOARD Y ESTADÍSTICAS
        // ---------------------------------------------------
        
        // Tablero Principal
        Route::get('tablero', [TableroInventarioController::class, 'index'])
            ->name('tablero');

        // API para datos de gráficos (AJAX)
        Route::get('tablero/data', [TableroInventarioController::class, 'getChartData'])
            ->name('tablero.data');


        // ---------------------------------------------------
        // 2. GESTIÓN DE COMPRAS (Entradas)
        // ---------------------------------------------------
        
        // Descargar Factura PDF
        Route::get('compras/{id}/descargar-factura', [CompraController::class, 'descargarFactura'])
            ->name('compras.descargar');

        // CRUD Completo de Compras
        Route::resource('compras', CompraController::class)
            ->parameters(['compras' => 'invCompra']);


        // ---------------------------------------------------
        // 3. OPERACIONES DE MOVIMIENTOS (Actas)
        // ---------------------------------------------------

        // Generar PDF del Acta (Entrega/Devolución)
        Route::get('movimientos/{id}/pdf', [MovimientoController::class, 'generarPdf'])
            ->name('movimientos.pdf');

        // CRUD Completo de Movimientos
        Route::resource('movimientos', MovimientoController::class)
            ->parameters(['movimientos' => 'invMovimiento']);


        // ---------------------------------------------------
        // 4. MANTENIMIENTOS Y REPARACIONES
        // ---------------------------------------------------
        
        Route::resource('mantenimientos', MantenimientoController::class)
            ->parameters(['mantenimientos' => 'invMantenimiento']);


        // ---------------------------------------------------
        // 5. ALMACÉN DE ACTIVOS (Núcleo)
        // ---------------------------------------------------

        // --- Rutas Específicas ---
        
        // Vista de Alertas (Garantías por vencer)
        Route::get('activos/alertas', [ActivoController::class, 'alertas'])
            ->name('activos.alertas');

        // Hoja de Vida (Vista detallada para imprimir)
        Route::get('activos/{id}/hoja-vida', [ActivoController::class, 'hojaVida'])
            ->name('activos.hoja_vida');

        // Buscador AJAX para autocompletado
        Route::get('activos/buscar', [ActivoController::class, 'buscarAjax'])
            ->name('activos.buscar');

        // --- CRUD Principal ---
        Route::resource('activos', ActivoController::class)
            ->parameters(['activos' => 'invActivo']);


        // ---------------------------------------------------
        // 6. CATÁLOGOS Y CONFIGURACIÓN
        // ---------------------------------------------------

        // Catálogos Simples
        Route::resource('marcas', MarcaController::class)
            ->parameters(['marcas' => 'invMarca']);

        Route::resource('bodegas', BodegaController::class)
            ->parameters(['bodegas' => 'invBodega']);

        Route::resource('estados', EstadoController::class)
            ->parameters(['estados' => 'invEstado']);

        Route::resource('metodos-pago', MetodoPagoController::class)
            ->parameters(['metodos-pago' => 'invMetodo']);


        // --- GESTIÓN AVANZADA DE CLASIFICACIÓN (Jerarquía) ---

        // 1. Rutas para CREAR Catálogos Base (POST)
        Route::post('clasificacion/store-grupo', [ClasificacionController::class, 'storeGrupo'])->name('clasificacion.grupo.store');
        Route::post('clasificacion/store-linea', [ClasificacionController::class, 'storeLinea'])->name('clasificacion.linea.store');
        Route::post('clasificacion/store-tipo', [ClasificacionController::class, 'storeTipo'])->name('clasificacion.tipo.store');

        // 2. Rutas para ACTUALIZAR Catálogos Base (PUT) - ¡NUEVO!
        Route::put('clasificacion/update-grupo/{id}', [ClasificacionController::class, 'updateGrupo'])->name('clasificacion.grupo.update');
        Route::put('clasificacion/update-linea/{id}', [ClasificacionController::class, 'updateLinea'])->name('clasificacion.linea.update');
        Route::put('clasificacion/update-tipo/{id}', [ClasificacionController::class, 'updateTipo'])->name('clasificacion.tipo.update');

        // 3. Ruta para ELIMINAR Paramétricos
        Route::delete('clasificacion/destroy-parametro/{id}/{tipo}', [ClasificacionController::class, 'destroyParametro'])->name('clasificacion.parametro.destroy');

        // 4. Resource Principal (Subgrupos - Tabla final)
        Route::resource('clasificacion', ClasificacionController::class)
            ->parameters(['clasificacion' => 'invSubgrupo']);


        // ---------------------------------------------------
        // 7. FILTROS DINÁMICOS (AJAX)
        // ---------------------------------------------------
        
        // Rutas para llenar selects dependientes vía JS
        Route::get('clasificacion/lineas/{grupo_id}', [ClasificacionController::class, 'getLineasByGrupo'])
            ->name('ajax.lineas');

        Route::get('clasificacion/subgrupos/{linea_id}', [ClasificacionController::class, 'getSubgruposByLinea'])
            ->name('ajax.subgrupos');

    });
// FIN MÓDULO INVENTARIOS

// ==========================================
//   MÓDULO DE CORRESPONDENCIA
// ==========================================
Route::middleware(['auth'])->prefix('correspondencia')->name('correspondencia.')->group(function () {

    // ---------------------------------------------------
    // 1. DASHBOARD (TABLERO UNIFICADO)
    // ---------------------------------------------------
    Route::get('tablero', [CorrespondenciaController::class, 'tablero'])
        ->name('tablero');

    // ---------------------------------------------------
    // 2. GESTIÓN DE CORRESPONDENCIA (CRUD)
    // ---------------------------------------------------
    Route::resource('correspondencias', CorrespondenciaController::class)
        ->parameters(['correspondencias' => 'correspondencia']);

    // AJAX: Consultas dinámicas para la UI
    Route::get('ajax/correspondencias-por-estado/{estado_id}', [CorrespondenciaController::class, 'getByEstado'])
        ->name('ajax.correspondencias.estado');

    Route::get('ajax/trds-por-flujo/{flujo_id}', [CorrespondenciaController::class, 'getTrdsByFlujo'])
        ->name('ajax.trds.flujo');

    // ---------------------------------------------------
    // 3. FLUJOS Y PROCESOS
    // ---------------------------------------------------
    Route::resource('flujos', FlujoDeTrabajoController::class)
        ->parameters(['flujos' => 'flujo']);

    Route::resource('procesos', ProcesoController::class)
        ->parameters(['procesos' => 'proceso']);

    // Gestión de Usuarios en Procesos
    Route::post('procesos/{proceso}/asignar-usuario', [ProcesoController::class, 'asignarUsuario'])
        ->name('procesos.asignarUsuario');

    Route::delete('procesos/{proceso}/remover-usuario/{user_id}', [ProcesoController::class, 'removerUsuario'])
        ->name('procesos.removerUsuario');

    // --- NUEVAS RUTAS DE ESTADOS PARA PROCESOS ---
    Route::post('procesos/{proceso}/guardar-estado', [ProcesoController::class, 'guardarEstado'])
        ->name('procesos.guardarEstado');

    Route::delete('procesos/estado/{id}', [ProcesoController::class, 'eliminarEstado'])
        ->name('procesos.eliminarEstado');
    // ----------------------------------------------

    Route::get('ajax/usuarios-proceso/{proceso_id}', [ProcesoController::class, 'getUsuariosByProceso'])
        ->name('ajax.usuarios.proceso');

    // ---------------------------------------------------
    // 4. NOTIFICACIONES
    // ---------------------------------------------------
    Route::resource('notificaciones', NotificacionController::class)
        ->parameters(['notificaciones' => 'notificacion']);

    Route::post('notificaciones/{notificacion}/marcar-leida', [NotificacionController::class, 'marcarLeida'])
        ->name('notificaciones.marcarLeida');

    // ---------------------------------------------------
    // 5. TRD (Tablas de Retención Documental)
    // ---------------------------------------------------
    Route::resource('trds', TrdController::class)
        ->parameters(['trds' => 'trd']);

    // ---------------------------------------------------
    // 6. PLANTILLAS
    // ---------------------------------------------------
    Route::resource('plantillas', PlantillaController::class)
        ->parameters(['plantillas' => 'plantilla']);

    // ---------------------------------------------------
    // 7. COMUNICACIONES DE SALIDA
    // ---------------------------------------------------
    Route::resource('comunicaciones-salida', ComunicacionSalidaController::class)
        ->parameters(['comunicaciones-salida' => 'comunicacionSalida']);

    Route::get('comunicaciones-salida/{comunicacionSalida}/descargar-pdf', [ComunicacionSalidaController::class, 'descargarPdf'])
        ->name('comunicaciones-salida.descargar');

    // ---------------------------------------------------
    // 8. TRACKING DE PROCESOS (HISTORIAL/SEGUIMIENTO)
    // ---------------------------------------------------
    Route::resource('correspondencias-procesos', CorrespondenciaProcesoController::class)
        ->parameters(['correspondencias-procesos' => 'correspondenciaProceso']);

    Route::post('correspondencias-procesos/{correspondenciaProceso}/marcar-notificado', [CorrespondenciaProcesoController::class, 'marcarNotificado'])
        ->name('correspondencias-procesos.marcarNotificado');

    // ---------------------------------------------------
    // 9. CONFIGURACIÓN DE ESTADOS (MAESTRO)
    // ---------------------------------------------------
    Route::resource('estados', CorrEstadoController::class)
        ->parameters(['estados' => 'estado']);
    
        // ---------------------------------------------------
    // 10. MEDIOS DE RECEPCIÓN (NUEVO)
    // ---------------------------------------------------
    Route::resource('medios-recepcion', MedioRecepcionController::class)
        ->parameters(['medios-recepcion' => 'medios_recepcion']);

});
// FIN MÓDULO DE CORRESPONDENCIA


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

//QUIZ TI
Route::get('/indicators/quiz', [QuizController::class, 'quizinicio'])->name('indicators.quiz.inicio');
Route::get('/indicators/quiz/{prueba}/preguntas',[QuizController::class, 'generarpreguntas'])->name('indicators.quiz.preguntas');
Route::post('/indicators/validarcorreo', [QuizController::class, 'validar'])->name('indicators.validar.correo');
Route::post('/indicators/quiz/store', [QuizController::class, 'storeQuiz'])->name('indicators.quiz.store');
Route::prefix('indicators')->group(function () {
    Route::resource('quizes', QuizController::class)->names('indicators.quizes')->middleware('auth');
});
//Indicadores
Route::prefix('indicators')->group(function () {
    Route::resource('indicador', IndicadoresController::class)->names('indicators.indicadores')->middleware('auth');
    Route::post('/generar/informe', [IndicadoresController::class, 'descargarInforme'])->name('indicators.indicadores.descargar')->middleware('auth');
});
