<?php

namespace App\Http\Controllers\Sgrh;

use App\Http\Controllers\AuditoriaController;
use App\Http\Controllers\Controller;
use App\Models\Sgrh\FestivoAjuste;
use App\Services\Sgrh\FestivoColombiaCalculador;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VacacionFestivoController extends Controller
{
    public function __construct(private FestivoColombiaCalculador $calculador)
    {
    }

    private function auditoria($accion)
    {
        app(AuditoriaController::class)->create($accion, 'SGRH');
    }

    public function index(Request $request)
    {
        $anio = (int) $request->input('anio', now()->year);

        return view('sgrh.vacacion.festivo.index', [
            'anio' => $anio,
            'festivos' => $this->calculador->festivosDelAnio($anio),
            'ajustes' => FestivoAjuste::with('usuario')->whereYear('fecha', $anio)->orderBy('fecha')->get(),
        ]);
    }

    /**
     * Fechas festivas de un año en JSON — usado por los calendarios de selección de fechas
     * (solicitud individual y calendario global) para pintar sábados/domingos/festivos, sin
     * necesitar el permiso de gestión de festivos: cualquier usuario autenticado del módulo
     * puede consultarlas, es información no sensible.
     */
    public function fechas(Request $request)
    {
        $anio = (int) $request->input('anio', now()->year);

        return response()->json(
            $this->calculador->festivosDelAnio($anio)->map(fn ($f) => $f->format('Y-m-d'))->values()
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'fecha' => 'required|date',
            'tipo' => 'required|in:agregado,excluido',
            'descripcion' => 'nullable|string|max:255',
        ]);

        $validated['user_id'] = Auth::id();

        FestivoAjuste::create($validated);

        $this->auditoria("Ajuste de festivo [{$validated['tipo']}]: {$validated['fecha']}" . (!empty($validated['descripcion']) ? " ({$validated['descripcion']})" : ''));

        return back()->with('success', 'Ajuste de festivo guardado correctamente.');
    }

    public function destroy(FestivoAjuste $festivo)
    {
        $descripcion = "{$festivo->tipo}: {$festivo->fecha->format('d/m/Y')}";
        $festivo->delete();

        $this->auditoria("Ajuste de festivo eliminado ({$descripcion})");

        return back()->with('success', 'Ajuste eliminado correctamente.');
    }
}
