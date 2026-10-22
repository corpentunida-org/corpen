<?php

namespace App\Http\Controllers\Rsv;

use App\Http\Controllers\Controller;
use App\Models\Rsv\Mensaje;
use App\Models\Rsv\Reserva;
use App\Models\Rsv\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Throwable;

class ClientePortalController extends Controller
{
    /** Mapa whitelisted de fragmentos AJAX (tab => vista). */
    private const FRAGMENTOS = [
        'nueva-buscador'       => 'rsv.cliente.partials.nueva-reserva.buscador',
        'nueva-checkout'       => 'rsv.cliente.partials.nueva-reserva.checkout',
        'reservas-activas'     => 'rsv.cliente.partials.mis-reservas.activas-pasadas',
        'reservas-itinerario'  => 'rsv.cliente.partials.mis-reservas.itinerario',
        'reservas-huespedes'   => 'rsv.cliente.partials.mis-reservas.huespedes',
        'historial-estados'    => 'rsv.cliente.partials.historial.estados',
        'historial-endosos'    => 'rsv.cliente.partials.historial.endosos',
        'chat-mensajes'        => 'rsv.cliente.partials.chat.mensajes',
        'chat-receptores'      => 'rsv.cliente.partials.chat.receptores',
        'pagos-transacciones'  => 'rsv.cliente.partials.pagos.transacciones',
        'pagos-pasarelas'      => 'rsv.cliente.partials.pagos.pasarelas',
        'pagos-reviews'        => 'rsv.cliente.partials.pagos.reviews',
    ];

    /**
     * Portal principal: KPIs del cliente autenticado.
     */
    public function index(Request $request): View
    {
        $userId = Auth::id();
        $hoy    = now()->startOfDay();

        $kpiTotal = $kpiActivas = $kpiAbonado = $kpiSaldo = 0;
        $kpiProximo = null;
        $misReservas = collect();

        try {
            $misReservas = Reserva::with(['inmueble', 'status'])
                ->where('id_user', $userId)
                ->orderByDesc('created_at')
                ->get();

            $kpiTotal    = $misReservas->count();
            $kpiProximo  = $misReservas
                ->map(fn ($r) => $r->fecha_inicio ? \Carbon\Carbon::parse($r->fecha_inicio)->startOfDay() : null)
                ->filter(fn ($f) => $f && $f->gte($hoy))
                ->sort()
                ->first();

            foreach ($misReservas as $r) {
                $fi = $r->fecha_inicio ? \Carbon\Carbon::parse($r->fecha_inicio)->startOfDay() : null;
                $ff = $r->fecha_fin   ? \Carbon\Carbon::parse($r->fecha_fin)->startOfDay()   : null;
                if ($fi && $ff && ($hoy->betweenIncluded($fi, $ff) || $fi->gte($hoy))) { $kpiActivas++; }

                $kpiSaldo += (float) ($r->monto_total ?? 0);
                foreach (($r->transacciones ?? collect()) as $tx) {
                    $edo = mb_strtolower((string) ($tx->estado_pago ?? ''));
                    if (str_contains($edo, 'anulad')) { continue; }
                    if (str_contains($edo, 'complet') || str_contains($edo, 'aprob') || str_contains($edo, 'pagad')) {
                        $kpiAbonado += (float) ($tx->monto ?? 0);
                    }
                }
            }
            $kpiSaldo = max(0, $kpiSaldo - $kpiAbonado);
        } catch (Throwable $e) { report($e); }

        return view('rsv.cliente.portal', [
            'kpiTotal'     => $kpiTotal,
            'kpiActivas'   => $kpiActivas,
            'kpiProximo'   => $kpiProximo,
            'kpiAbonado'   => $kpiAbonado,
            'kpiSaldo'     => $kpiSaldo,

            // CORRECCIÓN AQUÍ: Contar mensajes asociados a las reservas del cliente
            'kpiMensajes'  => class_exists(Mensaje::class)
                ? Mensaje::whereIn('id_rsv_reservas', $misReservas->pluck('id'))->count() : 0,

            'kpiReviews'   => class_exists(Review::class) ? Review::where('id_user_autor', $userId)->count() : 0,
            'kpiHistorial' => $kpiTotal,
            'misReservas'  => $misReservas->take(10),
        ]);
    }

    /**
     * Devuelve SOLO el fragmento solicitado (swap AJAX in-place del portal cliente).
     */
    public function fragmento(Request $request)
    {
        $key = (string) $request->query('tab', '');

        if (!isset(self::FRAGMENTOS[$key]) || !view()->exists(self::FRAGMENTOS[$key])) {
            return response('<div class="alert alert-warning m-3">Fragmento no disponible.</div>', 200);
        }

        return response()
            ->view(self::FRAGMENTOS[$key])
            ->header('Cache-Control', 'no-store, max-age=0');
    }
}
