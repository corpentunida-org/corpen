<?php

namespace App\Services\Certicados;

use App\Models\Interacciones\Interaction;
use App\Models\Certificados\CarSiaOperacion;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CrmInteractionService
{
    /**
     * Registra una interacción individual en el CRM.
     *
     * @param CarSiaOperacion $operacion
     * @param mixed $tipoCertificadoId
     * @param string $metodo
     * @return void
     */
    public function registrarGeneracionCertificado(CarSiaOperacion $operacion, $tipoCertificadoId, $metodo = 'Individual')
    {
        try {
            $lineasIds = $operacion->lineas()
                ->pluck('id_car_sia_lineas')
                ->filter()
                ->unique()
                ->values()
                ->toArray();

            Interaction::create([
                'client_id'              => $operacion->id_tercero,
                'agent_id'               => Auth::id(),
                'interaction_date'       => Carbon::now(),
                'interaction_channel'    => 9,  // App
                'interaction_type'       => 46, // Modulo Certificado
                'outcome'                => 2,  // Efectivo
                'duration'               => 0,
                'notes'                  => "Se generó un certificado [Tipo ID: {$tipoCertificadoId}] para el lote {$operacion->numero_bloque} mediante el módulo {$metodo}.",
                'id_linea_de_obligacion' => !empty($lineasIds) ? $lineasIds : null,
                'id_user_asignacion'     => Auth::id(),
            ]);

        } catch (\Throwable $e) {
            // \Throwable asegura capturar tanto Exceptions como errores fatales de PHP
            Log::error("SIA CRM - Error al registrar interacción para operación {$operacion->id}: " . $e->getMessage(), [
                'exception' => $e
            ]);
        }
    }

    /**
     * Registra interacciones de manera masiva
     *
     * @param iterable $operacionesChunk
     * @param mixed $tiposIds
     * @param string|int $bloque
     * @return void
     */
    public function registrarLoteMasivo($operacionesChunk, $tiposIds, $bloque)
    {
        try {
            $interacciones = [];
            $ahora = Carbon::now();
            $userId = Auth::id();

            // Verificamos si es array (Multi-select) o un string simple para el log
            $tiposString = is_array($tiposIds) ? implode(', ', $tiposIds) : $tiposIds;

            foreach ($operacionesChunk as $operacion) {
                $interacciones[] = [
                    'client_id'              => $operacion->id_tercero,
                    'agent_id'               => $userId,
                    'interaction_date'       => $ahora,
                    'interaction_channel'    => 9,  // App
                    'interaction_type'       => 46, // Modulo Certificado
                    'outcome'                => 2,  // Efectivo
                    'duration'               => 0,
                    'notes'                  => "Generación masiva de certificados [Tipos: {$tiposString}] - Lote {$bloque}",
                    // Codificamos el JSON vacío manualmente porque insert() salta los casts del modelo
                    'id_linea_de_obligacion' => json_encode([]),
                    'id_user_asignacion'     => $userId,
                    'created_at'             => $ahora,
                    'updated_at'             => $ahora,
                ];
            }

            if (!empty($interacciones)) {
                Interaction::insert($interacciones);
            }

        } catch (\Throwable $e) {
            Log::error("SIA CRM - Error al registrar interacciones masivas del bloque {$bloque}: " . $e->getMessage(), [
                'exception' => $e
            ]);
        }
    }
}
