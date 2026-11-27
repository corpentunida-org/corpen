<?php

namespace App\Console\Commands\Rsv;

use App\Http\Controllers\Rsv\ReservaController;
use App\Mail\ReservaInmueble;
use App\Models\Rsv\HistorialEstado;
use App\Models\Rsv\Reserva;
use App\Models\Rsv\Status;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Módulo Rsv · cancela las reservas PENDIENTES cuyo plazo de pago venció sin soporte.
 *
 * Plazo: ReservaController::DIAS_HABILES_PAGO días hábiles desde la creación (sin sábados,
 * domingos ni festivos de Colombia; fechaLimitePago()). La reserva se cancela al día siguiente
 * del último día del plazo. "Tiene soporte" = alguna transacción financiera vigente (no anulada
 * / no eliminada) con soporte_pago cargado.
 *
 * Por cada reserva cancelada: estado → Cancelada, historial de estados (auditoría) y correo
 * al asociado informando que perdió la reserva por no cargar el pago.
 *
 *   php artisan rsv:cancelar-sin-pago --dry-run   → solo lista, no cambia nada ni envía correos
 */
class CancelarReservasSinPago extends Command
{
    protected $signature = 'rsv:cancelar-sin-pago {--dry-run : Solo listar las reservas que se cancelarían}';

    protected $description = 'Rsv: cancela reservas pendientes sin soporte de pago tras el plazo de días hábiles y notifica al asociado';

    public function handle(): int
    {
        $pendiente = Status::whereRaw('LOWER(name) LIKE ?', ['%pendien%'])->orderBy('id')->first();
        $cancelada = Status::whereRaw('LOWER(name) LIKE ?', ['%cancela%'])->orderBy('id')->first();
        if (!$pendiente || !$cancelada) {
            $this->error('No se encontraron los estados "Pendiente" y/o "Cancelada" en rsv_statuses.');
            return self::FAILURE;
        }

        $hoy   = now()->startOfDay();
        $dry   = (bool) $this->option('dry-run');
        $total = 0;

        // Pre-filtro barato: el plazo de N días hábiles nunca es menor a N días calendario
        Reserva::with(['user', 'inmueble', 'transacciones'])
            ->where('id_rsv_statuses', $pendiente->id)
            ->whereNotNull('created_at')
            ->where('created_at', '<', $hoy->copy()->subDays(ReservaController::DIAS_HABILES_PAGO))
            ->whereDoesntHave('transacciones', fn ($q) => self::soporteValido($q))
            ->orderBy('id')
            ->chunkById(100, function ($reservas) use ($hoy, $dry, $pendiente, $cancelada, &$total) {
                foreach ($reservas as $reserva) {
                    // Desde la creación o desde el último soporte rechazado (plazo nuevo)
                    $limite = ReservaController::fechaLimitePagoDe($reserva);
                    if (!$limite || $limite->gte($hoy)) {
                        continue; // todavía está dentro del plazo (el último día cuenta completo)
                    }

                    $total++;
                    $linea = sprintf('#%d %s · %s · creada %s · límite %s',
                        $reserva->id, $reserva->codigo_reserva, optional($reserva->user)->name ?? 'sin asociado',
                        Carbon::parse($reserva->created_at)->format('Y-m-d'), $limite->format('Y-m-d'));

                    if ($dry) {
                        $this->line('[dry-run] ' . $linea);
                        continue;
                    }

                    try {
                        DB::transaction(function () use ($reserva, $pendiente, $cancelada, $limite) {
                            // Revalida bajo bloqueo: pudo cargar el soporte o cambiar de estado mientras tanto
                            $fresca = Reserva::lockForUpdate()->find($reserva->id);
                            if (!$fresca || (int) $fresca->id_rsv_statuses !== (int) $pendiente->id
                                || self::soporteValido($fresca->transacciones())->exists()) {
                                throw new \RuntimeException('omitida');
                            }
                            $fresca->update(['id_rsv_statuses' => $cancelada->id]);
                            HistorialEstado::create([
                                'id_rsv_reservas'          => $fresca->id,
                                'id_rsv_statuses_anterior' => $pendiente->id,
                                'id_rsv_statuses_nuevo'    => $cancelada->id,
                                'id_user'                  => null,
                                'comentario'               => 'Cancelación automática: no se cargó el soporte de pago dentro de '
                                    . ReservaController::DIAS_HABILES_PAGO . ' días hábiles (límite ' . $limite->format('d/m/Y') . ').',
                            ]);
                        });
                    } catch (\RuntimeException $e) {
                        if ($e->getMessage() === 'omitida') { $total--; continue; }
                        throw $e;
                    } catch (\Throwable $e) {
                        $total--;
                        Log::error('rsv:cancelar-sin-pago reserva ' . $reserva->id . ': ' . $e->getMessage());
                        $this->error('Error en ' . $linea . ': ' . $e->getMessage());
                        continue;
                    }

                    $this->info('Cancelada ' . $linea);
                    $this->notificar($reserva, $limite);
                }
            });

        $this->info(($dry ? '[dry-run] Se cancelarían ' : 'Canceladas ') . $total . ' reserva(s) sin soporte de pago.');
        return self::SUCCESS;
    }

    /**
     * Transacciones con soporte que cuenta: archivo real (no vacío ni "0") y no rechazado / anulado /
     * fallido por contabilidad. Misma regla que TransaccionFinanciera::esSoporteValido().
     */
    private static function soporteValido($q)
    {
        return $q->whereNotNull('soporte_pago')
            ->whereNotIn('soporte_pago', ['', '0'])
            ->where(function ($w) {
                $w->whereNull('estado_pago')
                  ->orWhere(function ($e) {
                      foreach (['%rechaz%', '%anul%', '%cancel%', '%fallid%'] as $k) {
                          $e->whereRaw('LOWER(estado_pago) NOT LIKE ?', [$k]);
                      }
                  });
            });
    }

    /** Correo al asociado: perdió la reserva por no cargar el pago. Un fallo solo se registra. */
    private function notificar(Reserva $reserva, Carbon $limite): void
    {
        try {
            $user = $reserva->user;
            if (!$user || empty($user->email)) {
                Log::warning('rsv:cancelar-sin-pago: asociado sin correo', ['reserva' => $reserva->id]);
                return;
            }
            $inmueble = optional($reserva->inmueble)->name ?? 'el inmueble';
            $fmt = fn ($f) => Carbon::parse($f)->locale('es')->translatedFormat('l j \d\e F \d\e Y');

            $texto = 'Le informamos que su reserva <strong>' . e($reserva->codigo_reserva) . '</strong> en <strong>' . e($inmueble) . '</strong> '
                . '(llegada ' . e($fmt($reserva->fecha_inicio)) . ', salida ' . e($fmt($reserva->fecha_fin)) . ') '
                . 'fue <strong>cancelada automáticamente</strong> porque no se recibió el soporte de pago dentro del plazo de '
                . '<strong>' . ReservaController::DIAS_HABILES_PAGO . ' días hábiles</strong>, que venció el <strong>' . e($fmt($limite)) . '</strong>.<br><br>'
                . 'Las fechas quedaron disponibles para otros asociados. Si aún desea hospedarse, puede realizar una nueva reserva '
                . 'desde el Portal de Reservas, sujeta a disponibilidad. ¡Dios le bendiga!';

            Mail::to($user->email)->send(new ReservaInmueble(
                $user->name,
                $texto,
                'Reserva ' . $reserva->codigo_reserva . ' cancelada por falta de pago',
                false,
                null,
                url(route('rsv.cliente.portal')) . '?tab=nueva-reserva',
                'Hacer una nueva reserva'
            ));
        } catch (\Throwable $e) {
            Log::error('rsv:cancelar-sin-pago correo reserva ' . $reserva->id . ': ' . $e->getMessage());
        }
    }
}
