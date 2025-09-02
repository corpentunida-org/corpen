<?php

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InteractionController;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



Route::prefix('api')->group(function () {
    // Ruta para almacenar una nueva interacción (incluyendo la subida de archivos)
    Route::post('/interactions', [InteractionController::class, 'store'])->name('api.interactions.store');

    // Ruta para actualizar una interacción existente (incluyendo la gestión de archivos)
    Route::post('/interactions/{interaction}', [InteractionController::class, 'update'])->name('api.interactions.update');
    // Nota: A menudo se usa PUT/PATCH para actualizaciones, pero POST funciona si tu frontend lo maneja así.
    // Si quieres ser RESTful, podrías usar:
    // Route::put('/interactions/{interaction}', [InteractionController::class, 'update'])->name('api.interactions.update');
    // Route::patch('/interactions/{interaction}', [InteractionController::class, 'update'])->name('api.interactions.update');


    // Ruta para eliminar una interacción
    Route::delete('/interactions/{interaction}', [InteractionController::class, 'destroy'])->name('api.interactions.destroy');

    // Opcional: Rutas para obtener interacciones individuales o listados via API
    Route::get('/interactions', [InteractionController::class, 'index'])->name('api.interactions.index');
    Route::get('/interactions/{interaction}', [InteractionController::class, 'show'])->name('api.interactions.show');
});