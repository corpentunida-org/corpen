<?php

namespace App\Traits;

use App\Models\Certificados\CarSiaOperacionLog;
use Illuminate\Support\Facades\Auth;

trait LogAuditoriaTrait
{
    /**
     * Registra un evento en la auditoría con una estructura JSON estandarizada.
     */
    protected function registrarLogAuditoria(
        $numeroBloque,
        $idOrigenEvento,
        $idEventoAuditoria,
        $accion,
        $entidadAfectada,
        $descripcion,
        $identificadores = [],
        $metricas = [],
        $parametros = [],
        $contexto = []
    ) {
        // Combinar datos recibidos con la estructura base para garantizar que las llaves SIEMPRE existan
        $detallesEjecucion = [
            'accion'           => $accion,
            'entidad_afectada' => $entidadAfectada,
            'descripcion'      => $descripcion,
            
            'identificadores'  => array_merge([
                'id_operacion'      => null,
                'id_registro_api'   => null,
                'id_factura'        => null,
                'documento_tercero' => null,
                'numero_radicado'   => null,
            ], $identificadores),
            
            'metricas'         => array_merge([
                'registros_procesados' => 0,
                'registros_ignorados'  => 0,
                'registros_afectados'  => 0,
                'valor_financiero'     => null,
            ], $metricas),
            
            'parametros'       => array_merge([
                'estado_asignado'  => null,
                'tipo_asignado'    => null,
                'alerta_asignada'  => null,
                'fecha_programada' => null,
                'hash_generado'    => null,
            ], $parametros),
            
            'contexto'         => array_merge([
                'nombre_archivo' => null,
                'error_tecnico'  => null,
                'observaciones'  => null,
            ], $contexto),
        ];

        return CarSiaOperacionLog::create([
            'numero_bloque'                 => $numeroBloque,
            'id_car_sia_operaciones_lineas' => $identificadores['id_linea_operacion'] ?? null, 
            'id_car_sia_origenes_evento'    => $idOrigenEvento,
            'id_car_sia_eventos_auditoria'  => $idEventoAuditoria,
            'id_user'                       => Auth::check() ? Auth::id() : null,
            'ip'                            => request()->ip() ?? '127.0.0.1',
            'detalles_ejecucion'            => $detallesEjecucion,
        ]);
    }
}