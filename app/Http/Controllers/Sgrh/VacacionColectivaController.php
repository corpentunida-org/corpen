<?php

namespace App\Http\Controllers\Sgrh;

use App\Http\Controllers\AuditoriaController;
use App\Http\Controllers\Controller;
use App\Models\Sgrh\Area;
use App\Models\Sgrh\Empleado;
use App\Models\Sgrh\VacacionColectiva;
use App\Models\Sgrh\VacacionSolicitud;
use App\Services\Sgrh\VacacionValidador;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VacacionColectivaController extends Controller
{
    public function __construct(private VacacionValidador $validador)
    {
    }

    private function auditoria($accion)
    {
        app(AuditoriaController::class)->create($accion, 'SGRH');
    }

    public function index()
    {
        $colectivas = VacacionColectiva::with('usuario')->latest('fecha_inicio')->paginate(20);

        return view('sgrh.vacacion.colectiva.index', compact('colectivas'));
    }

    public function create()
    {
        return view('sgrh.vacacion.colectiva.create', [
            'areas' => Area::orderBy('nombre')->get(),
            'empleados' => Empleado::with('tercero')->where('estado', 'activo')->get()->sortBy('nombre_completo'),
        ]);
    }

    /**
     * 'obligatoria' genera de una vez una solicitud YA APROBADA por cada empleado del alcance
     * elegido (descuenta su saldo sin que tengan que solicitarla). 'bloqueo' no genera nada —
     * solo queda como regla que VacacionValidador consulta al validar solicitudes individuales.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tipo' => 'required|in:obligatoria,bloqueo',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'descripcion' => 'required|string|max:500',
            'alcance' => 'required|in:empresa,area,empleados',
            'areas' => 'required_if:alcance,area|array',
            'areas.*' => 'exists:sgrh_areas,id',
            'empleados' => 'required_if:alcance,empleados|array',
            'empleados.*' => 'exists:sgrh_empleados,id',
        ]);

        $colectiva = DB::transaction(function () use ($validated) {
            $colectiva = VacacionColectiva::create([
                'tipo' => $validated['tipo'],
                'fecha_inicio' => $validated['fecha_inicio'],
                'fecha_fin' => $validated['fecha_fin'],
                'descripcion' => mb_strtoupper(trim(preg_replace('/\s+/', ' ', $validated['descripcion'])), 'UTF-8'),
                'alcance' => $validated['alcance'],
                'user_id' => Auth::id(),
            ]);

            if ($validated['alcance'] === 'area') {
                foreach ($validated['areas'] as $areaId) {
                    $colectiva->alcances()->create(['tipo' => 'area', 'referencia_id' => $areaId]);
                }
            } elseif ($validated['alcance'] === 'empleados') {
                foreach ($validated['empleados'] as $empleadoId) {
                    $colectiva->alcances()->create(['tipo' => 'empleado', 'referencia_id' => $empleadoId]);
                }
            }

            if ($validated['tipo'] === 'obligatoria') {
                $this->generarSolicitudesObligatorias($colectiva);
            }

            return $colectiva;
        });

        $this->auditoria("Vacaciones colectivas decretadas [{$colectiva->tipo}] {$colectiva->fecha_inicio->format('d/m/Y')} a {$colectiva->fecha_fin->format('d/m/Y')}, alcance: {$colectiva->alcance}");

        return redirect()->route('sgrh.vacacion.colectiva.index')->with('success', 'Vacaciones colectivas decretadas correctamente.');
    }

    private function generarSolicitudesObligatorias(VacacionColectiva $colectiva): void
    {
        $dias = $this->validador->calcularDiasHabiles($colectiva->fecha_inicio, $colectiva->fecha_fin);

        Empleado::where('estado', 'activo')->get()
            ->filter(fn (Empleado $e) => $colectiva->aplicaA($e))
            ->each(function (Empleado $empleado) use ($colectiva, $dias) {
                VacacionSolicitud::create([
                    'empleado_id' => $empleado->id,
                    'fecha_inicio' => $colectiva->fecha_inicio,
                    'fecha_fin' => $colectiva->fecha_fin,
                    'dias_habiles' => (string) $dias,
                    'tipo' => 'colectiva_obligatoria',
                    'estado' => 'aprobada',
                    'aprobador_user_id' => Auth::id(),
                    'rol_aprobador' => 'rrhh',
                    'fecha_resolucion' => now(),
                    'vacacion_colectiva_id' => $colectiva->id,
                ]);
            });
    }

    /**
     * Anula el decreto (no lo borra): las solicitudes ya generadas por una obligatoria quedan
     * como evidencia histórica de qué las originó, pero se cancelan (no se eliminan) para
     * devolver el saldo descontado — VacacionSaldoCalculador::diasTomados() solo suma las
     * 'aprobada'.
     */
    public function destroy(VacacionColectiva $colectiva)
    {
        DB::transaction(function () use ($colectiva) {
            $colectiva->update(['estado' => 'anulada']);
            $colectiva->solicitudes()->where('estado', 'aprobada')->update(['estado' => 'cancelada']);
        });

        $this->auditoria("Vacaciones colectivas #{$colectiva->id} anuladas [{$colectiva->tipo}] {$colectiva->fecha_inicio->format('d/m/Y')} a {$colectiva->fecha_fin->format('d/m/Y')}");

        return back()->with('success', 'Decreto anulado correctamente.');
    }
}
