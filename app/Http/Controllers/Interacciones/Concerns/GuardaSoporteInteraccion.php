<?php

namespace App\Http\Controllers\Interacciones\Concerns;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Compartido por los 3 puntos donde se sube un soporte (adjunto) de una interacción: crear
 * interacción, editar interacción, y el modal "Registrar seguimiento". Antes cada uno tenía su
 * propia carpeta en S3 (corpentunida/interacciones/evidencia_.../, corpentunida/daytrack/{id}/,
 * seguimientos/adjuntos/) y su propio límite de tamaño/tipos — confuso para quien sube el
 * archivo (el mismo archivo se aceptaba en un formulario y se rechazaba en otro) y para quien
 * después necesita encontrarlo. Regla de negocio: 5MB (ajustado 2026-09-23, antes 10MB),
 * jpg/jpeg/png/pdf/doc/docx, para los 3 puntos por igual — ver reglaValidacionSoporte().
 *
 * Lo ya subido antes de este cambio se queda donde está: getFile()/temporaryUrl() lee la ruta
 * guardada tal cual, sin importar la carpeta, así que no hace falta migrar nada.
 */
trait GuardaSoporteInteraccion
{
    /** Regla lista para pasarle a Request::validate(): ['attachment' => $this->reglaValidacionSoporte()]. */
    private function reglaValidacionSoporte(): string
    {
        return 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120';
    }

    /**
     * Sube el archivo y devuelve la ruta guardada en S3 (lo que va en attachment_urls).
     * Carpeta por AÑO de subida y luego por interacción — corpentunida/interacciones/{año}/{id}/
     * — para que Admin → Limpiar Historial de Adjuntos pueda ubicar y depurar por año más
     * rápido, y para que el propio bucket quede navegable por año en la consola de S3. El año es
     * el de HOY (cuándo se sube el archivo), no el de la interacción, porque es lo que de verdad
     * se quiere limpiar con el tiempo. Nombre único (timestamp + token corto) para que dos
     * soportes con el mismo nombre original no se pisen entre sí.
     *
     * Lo subido ANTES de este cambio (sin año en la ruta) se queda donde está — Admin → Limpiar
     * Historial de Adjuntos no depende de esta carpeta para encontrarlos: identifica los
     * archivos a borrar por la fecha guardada en la base de datos (int_seguimiento.created_at),
     * no por prefijo de S3, así que funciona igual para lo viejo y lo nuevo.
     */
    private function guardarSoporte(UploadedFile $file, int $interactionId): string
    {
        $folderPath = 'corpentunida/interacciones/'.now()->year."/{$interactionId}";
        $nombreSeguro = time().'_'.Str::random(8).'_'.str_replace(' ', '_', $file->getClientOriginalName());

        return Storage::disk('s3')->putFileAs($folderPath, $file, $nombreSeguro);
    }
}
