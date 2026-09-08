<?php

namespace App\Http\Controllers\Sgrh;

use App\Http\Controllers\AuditoriaController;
use App\Http\Controllers\Controller;
use App\Models\Sgrh\Empleado;
use App\Models\Sgrh\VacacionPolitica;
use App\Services\Sgrh\VacacionSaldoCalculador;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VacacionSaldoController extends Controller
{
    public function __construct(private VacacionSaldoCalculador $calculador)
    {
    }

    private function auditoria($accion)
    {
        app(AuditoriaController::class)->create($accion, 'SGRH');
    }

    /**
     * Listado global de saldos (RRHH). El cálculo se hace en memoria sobre la página actual —
     * no hay un total materializado que se pueda ordenar/filtrar directo en SQL (ver
     * VacacionSaldoCalculador).
     */
    public function index(Request $request)
    {
        $query = Empleado::with('tercero', 'contratoActivo.cargo.area')->where('estado', 'activo');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('tercero', function ($q) use ($search) {
                $q->where('nom1', 'like', "%{$search}%")
                    ->orWhere('nom2', 'like', "%{$search}%")
                    ->orWhere('apl1', 'like', "%{$search}%")
                    ->orWhere('apl2', 'like', "%{$search}%");
            });
        }

        $empleados = $query->get()->sortBy('nombre_completo');
        $politica = VacacionPolitica::vigente();

        $paginado = new \Illuminate\Pagination\LengthAwarePaginator(
            $empleados->forPage($request->input('page', 1), 20)->values(),
            $empleados->count(),
            20,
            $request->input('page', 1),
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $saldos = $paginado->getCollection()->mapWithKeys(fn (Empleado $e) => [
            $e->id => $this->calculador->saldoActual($e, $politica),
        ]);

        return view('sgrh.vacacion.saldo.index', ['empleados' => $paginado, 'saldos' => $saldos]);
    }

    public function show(Empleado $empleado)
    {
        $empleado->load('tercero', 'contratoActivo.cargo.area', 'vacacionSolicitudes', 'vacacionAjustes.usuario');
        $politica = VacacionPolitica::vigente();

        return view('sgrh.vacacion.saldo.show', [
            'empleado' => $empleado,
            'saldo' => $this->calculador->saldoActual($empleado, $politica),
            'diasCausados' => $this->calculador->diasCausados($empleado, $politica),
            'diasTomados' => $this->calculador->diasTomados($empleado),
            'politica' => $politica,
        ]);
    }

    public function storeAjuste(Request $request, Empleado $empleado)
    {
        $validated = $request->validate([
            'dias' => 'required|numeric|min:-100|max:100|not_in:0',
            'motivo' => 'required|string|max:255',
        ]);

        $empleado->vacacionAjustes()->create([
            'dias' => $validated['dias'],
            'motivo' => mb_strtoupper(trim(preg_replace('/\s+/', ' ', $validated['motivo'])), 'UTF-8'),
            'user_id' => Auth::id(),
        ]);

        $this->auditoria("Ajuste manual de saldo de vacaciones: {$validated['dias']} días para colaborador #{$empleado->id} ({$validated['motivo']})");

        return back()->with('success', 'Ajuste de saldo registrado correctamente.');
    }
}
