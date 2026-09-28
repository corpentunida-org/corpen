<?php

namespace App\Services\Soportes;

use App\Mail\SoporteCierreAutomaticoMail;
use App\Models\Soportes\ScpAlertaConfig;
use App\Models\Soportes\ScpEstado;
use App\Models\Soportes\ScpObservacion;
use App\Models\Soportes\ScpSoporte;
use App\Models\Soportes\ScpTipoObservacion;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Cierre automático: un ticket en "En Revisión" que lleva X días (dias_cierre_automatico, editable
 * desde Admin → Configuración de Alertas de Soportes) sin moverse de ahí se cierra solo, dejando
 * una observación de sistema con el motivo. Antes vivía como método público en
 * ScpSoporteController y se ejecutaba EN CADA visita a la lista de soportes (con subconsultas
 * correlacionadas) — cualquiera que abriera la pantalla, sin importar cuántas veces, pagaba ese
 * costo. Ahora vive acá, para que lo use tanto el comando programado (soportes:cerrar-automatico)
 * como el disparador de respaldo en el controlador (que sí lo throttlea con caché).
 */
class CierreAutomaticoService
{
    /** @return int cuántos tickets se cerraron */
    public function ejecutar(): int
    {
        $idRevision = ScpEstado::idPorNombre('Revision');
        $idCerrado = ScpEstado::idPorNombre('Cerrado');
        $idComentario = ScpTipoObservacion::idPorNombre('Comentario');

        // Si el catálogo no tiene estos nombres (entorno raro/incompleto), no hay nada seguro que
        // cerrar — mejor no hacer nada que cerrar con un id equivocado.
        if (!$idRevision || !$idCerrado) {
            return 0;
        }

        $diasEspera = max(1, ScpAlertaConfig::actual()->dias_cierre_automatico ?? 5);
        $fechaLimite = Carbon::now()->subDays($diasEspera);
        $soportes = ScpSoporte::where('scp_soportes.estado', $idRevision)
            ->whereExists(function ($query) use ($idRevision, $fechaLimite) {
                $query
                    ->selectRaw(1)
                    ->from('scp_observaciones as obs')
                    ->whereColumn('obs.id_scp_soporte', 'scp_soportes.id')
                    ->where('obs.id_scp_estados', $idRevision)
                    ->whereRaw(
                        'obs.created_at = (
                            SELECT MAX(created_at)
                            FROM scp_observaciones
                            WHERE id_scp_soporte = scp_soportes.id
                            AND id_scp_estados = ?
                        )',
                        [$idRevision],
                    )
                    ->whereDate('obs.created_at', '<=', $fechaLimite);
            })
            ->get();

        foreach ($soportes as $soporte) {
            $soporte->estado = $idCerrado;
            $soporte->save();

            ScpObservacion::create([
                'observacion' => 'Cierre automático del soporte después de '.$diasEspera.' día(s) en estado "En Revisión".',
                'timestam' => now(),
                'id_scp_soporte' => $soporte->id,
                'id_scp_estados' => $idCerrado,
                'id_tipo_observacion' => $idComentario ?? 1,
                'calcification' => 5,
            ]);

            $this->avisarCierre($soporte, $diasEspera);
        }

        return $soportes->count();
    }

    /**
     * Avisa por correo que el cierre fue automático (por vencimiento), no una solución real — al
     * creador siempre, y a quien lo tenía asignado para revisar si aplica. Envuelto en try/catch:
     * un correo que falla no debe impedir que el cierre en sí (ya guardado arriba) se cuente como
     * hecho.
     */
    private function avisarCierre(ScpSoporte $soporte, int $diasEspera): void
    {
        $soporte->loadMissing(['usuario', 'scpUsuarioAsignado.UserApp', 'prioridad', 'tipo']);

        try {
            if ($soporte->usuario && !empty($soporte->usuario->email)) {
                Mail::to($soporte->usuario->email)->send(new SoporteCierreAutomaticoMail($soporte, 'creador', $diasEspera));
            }

            $emailRevisor = optional($soporte->scpUsuarioAsignado?->UserApp)->email;
            if ($emailRevisor) {
                Mail::to($emailRevisor)->send(new SoporteCierreAutomaticoMail($soporte, 'revisor', $diasEspera));
            }
        } catch (\Throwable $e) {
            Log::error('Error enviando correo de cierre automático (soporte '.$soporte->id.'): '.$e->getMessage());
        }
    }
}
