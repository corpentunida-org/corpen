<?php

namespace App\Http\Controllers\Sgrh;

use App\Http\Controllers\AuditoriaController;
use App\Http\Controllers\Controller;
use App\Models\Sgrh\Cargo;
use App\Models\Sgrh\Contrato;
use App\Models\Sgrh\ContratoModificacion;
use App\Models\Sgrh\Empleado;
use App\Models\Sgrh\TipoContrato;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ContratoController extends Controller
{
    private function auditoria($accion)
    {
        app(AuditoriaController::class)->create($accion, 'SGRH');
    }

    // Sin 'Vencido' explícito en el formulario: siempre se calcula en vivo (Contrato::estaVencido)
    // y lo asigna el comando programado — pero se acepta como valor guardado por si alguien lo
    // marca manualmente, así que sigue siendo válido en la validación.
    private const ESTADOS = ['Activo', 'Vencido', 'Liquidado', 'Renovado'];

    private const CAUSALES_MODIFICACION = ['Cambio de cargo o salario', 'Cambio de ubicación', 'Otra'];

    public function index(Request $request)
    {
        $query = Contrato::with(['empleado.tercero', 'tipoContrato', 'cargo']);

        if ($request->filled('empleado_id')) {
            $query->where('empleado_id', $request->empleado_id);
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('tipo_contrato_id')) {
            $query->where('tipo_contrato_id', $request->tipo_contrato_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('empleado.tercero', function ($q) use ($search) {
                $q->where('nom1', 'like', "%{$search}%")
                    ->orWhere('nom2', 'like', "%{$search}%")
                    ->orWhere('apl1', 'like', "%{$search}%")
                    ->orWhere('apl2', 'like', "%{$search}%");
            });
        }

        $contratos = $query->latest('fecha_inicio')->paginate(20)->appends($request->query());
        $tiposContrato = TipoContrato::where('activo', true)->orderBy('nombre')->get();

        return view('sgrh.contrato.index', compact('contratos', 'tiposContrato'));
    }

    /**
     * Contratos vencidos o próximos a vencer en 30/60 días. "Vencido" se calcula siempre en
     * vivo desde fecha_vencimiento (Contrato::estaVencido), nunca desde el campo `estado`
     * guardado — así el dato mostrado nunca queda desactualizado.
     */
    public function alertas(Request $request)
    {
        $dias = (int) $request->input('dias', 30);
        $dias = in_array($dias, [30, 60], true) ? $dias : 30;

        $base = Contrato::with(['empleado.tercero', 'tipoContrato'])
            ->where('estado', 'Activo')
            ->whereNotNull('fecha_vencimiento')
            ->whereDate('fecha_vencimiento', '<=', now()->addDays($dias));

        $todos = (clone $base)->get();
        $totalVencidos = $todos->filter(fn($c) => $c->estaVencido)->count();
        $totalPorVencer = $todos->count() - $totalVencidos;

        $filtro = $request->input('filtro');
        if ($filtro === 'vencido') {
            $base->whereDate('fecha_vencimiento', '<', now());
        } elseif ($filtro === 'por_vencer') {
            $base->whereDate('fecha_vencimiento', '>=', now());
        }

        $contratos = $base->orderBy('fecha_vencimiento')->paginate(20)->appends($request->query());

        return view('sgrh.contrato.alertas', compact('contratos', 'dias', 'filtro', 'totalVencidos', 'totalPorVencer'));
    }

    public function create(Request $request)
    {
        $empleadoId = $request->query('empleado_id');

        return view('sgrh.contrato.create', [
            'empleadoSeleccionado' => $empleadoId ? Empleado::with('tercero')->find($empleadoId) : null,
            'empleados' => Empleado::with('tercero')->get()->sortBy('nombre_completo'),
            'tiposContrato' => TipoContrato::where('activo', true)->orderBy('nombre')->get(),
            'cargos' => Cargo::with('area')->where('activo', true)->orderBy('nombre')->get(),
            'cargoIdPrefill' => $request->query('cargo_id'),
            'tipoContratoIdPrefill' => $request->query('tipo_contrato_id'),
            'fechaInicioPrefill' => $request->query('fecha_inicio'),
            'fechaVencimientoPrefill' => $request->query('fecha_vencimiento'),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validado($request);

        if ($this->tieneOtroContratoActivo($validated['empleado_id'], $validated['estado'])) {
            return back()->withInput()
                ->with('error', 'Este colaborador ya tiene un contrato activo. Ciérralo o renuévalo antes de crear uno nuevo.');
        }

        if ($validated['estado'] === 'Liquidado' && empty($validated['fecha_terminacion_real'])) {
            $validated['fecha_terminacion_real'] = now()->format('Y-m-d');
        }

        $contrato = Contrato::create($validated);

        $this->auditoria("Contrato creado #{$contrato->id} para colaborador #{$contrato->empleado_id}");

        $this->sincronizarEstadoColaborador($contrato->empleado);

        return redirect()->route('sgrh.empleado.edit', $contrato->empleado_id)
            ->with('success', 'Contrato registrado correctamente.');
    }

    public function edit(Contrato $contrato)
    {
        $contrato->load('empleado.tercero', 'modificaciones.usuario');

        return view('sgrh.contrato.edit', [
            'contrato' => $contrato,
            'tiposContrato' => TipoContrato::where('activo', true)->orderBy('nombre')->get(),
            'cargos' => Cargo::with('area')->where('activo', true)->orderBy('nombre')->get(),
            'estados' => self::ESTADOS,
            'causalesModificacion' => self::CAUSALES_MODIFICACION,
        ]);
    }

    public function update(Request $request, Contrato $contrato)
    {
        $validated = $this->validado($request);
        $modificacion = $this->validadoModificacion($request);

        if ($this->tieneOtroContratoActivo($validated['empleado_id'], $validated['estado'], $contrato->id)) {
            return back()->withInput()->with('error', 'Este colaborador ya tiene otro contrato activo.');
        }

        if ($validated['estado'] === 'Liquidado' && empty($validated['fecha_terminacion_real']) && empty($contrato->fecha_terminacion_real)) {
            $validated['fecha_terminacion_real'] = now()->format('Y-m-d');
        }

        $contrato->update($validated);

        $this->auditoria("Contrato actualizado #{$contrato->id} (colaborador #{$contrato->empleado_id})");

        // Cada edición de contrato deja un registro propio en sgrh_contrato_modificaciones
        // (causal + observación), aparte del log genérico de Auditoria — es lo que permite
        // luego consultar específicamente "por qué cambió este contrato", no solo "que cambió".
        $this->registrarModificacion($contrato, $modificacion['causal_modificacion'], $modificacion['observacion_modificacion'] ?? null);

        $this->sincronizarEstadoColaborador($contrato->empleado);

        return redirect()->route('sgrh.empleado.edit', $contrato->empleado_id)
            ->with('success', 'Contrato actualizado correctamente.');
    }

    /**
     * Cierra el contrato vigente (estado='Renovado') y redirige al formulario de alta con
     * cargo/tipo heredados precargados — evita tener que editar dos registros a mano para el
     * caso de uso de renovación anual / cambio de tipo de contrato.
     */
    public function renovar(Contrato $contrato)
    {
        if ($contrato->estado !== 'Activo') {
            return back()->with('error', 'Solo se puede renovar un contrato activo.');
        }

        $contrato->estado = 'Renovado';
        $contrato->fecha_terminacion_real = $contrato->fecha_terminacion_real ?? now();
        $contrato->save();

        $this->auditoria("Contrato #{$contrato->id} renovado (colaborador #{$contrato->empleado_id})");

        // La renovación también es una modificación del contrato (cierra su vigencia) — sin
        // formulario propio para elegir causal, así que se registra con una causal fija y
        // distinta de 'Otra', para poder identificarla claramente en el historial.
        $this->registrarModificacion($contrato, 'Renovación', 'Contrato renovado: se cierra y se crea uno nuevo.');

        // El colaborador queda momentáneamente sin contrato activo hasta que se complete el
        // formulario de alta que sigue a continuación — sincronizarEstadoColaborador() lo
        // refleja de inmediato (lo vuelve a activar solo cuando se guarde el contrato nuevo).
        $this->sincronizarEstadoColaborador($contrato->empleado);

        // Sugerencia para el caso típico de aquí (término fijo < 1 año, renovación anual): el
        // nuevo contrato empieza al día siguiente del vencimiento del anterior y dura 1 año —
        // el formulario la precarga pero sigue siendo editable, no se fuerza.
        $fechaInicioSugerida = ($contrato->fecha_vencimiento ?? now())->copy()->addDay();
        $fechaVencimientoSugerida = $fechaInicioSugerida->copy()->addYear();

        return redirect()->route('sgrh.contrato.create', [
            'empleado_id' => $contrato->empleado_id,
            'cargo_id' => $contrato->cargo_id,
            'tipo_contrato_id' => $contrato->tipo_contrato_id,
            'fecha_inicio' => $fechaInicioSugerida->format('Y-m-d'),
            'fecha_vencimiento' => $fechaVencimientoSugerida->format('Y-m-d'),
        ])->with('success', 'Contrato anterior cerrado. Completa los datos del nuevo contrato.');
    }

    /**
     * Borrado real, reservado a sgrh.contrato.destroy (rol admin). Los contratos son historial
     * legal/laboral — por eso el resto de acciones (index/store/update) nunca lo permiten y solo
     * se cierran/editan — pero el admin con CRUD completo puede corregir un registro erróneo.
     * Se arrastra también su historial de modificaciones (no puede quedar huérfano: contrato_id
     * es RESTRICT, no cascade) y se resincroniza el estado del colaborador por si el contrato
     * borrado era el activo.
     */
    public function destroy(Contrato $contrato)
    {
        $empleadoId = $contrato->empleado_id;
        $descripcion = "{$contrato->tipoContrato->nombre} ({$contrato->fecha_inicio->format('d/m/Y')})";

        DB::transaction(function () use ($contrato) {
            $contrato->modificaciones()->delete();
            $contrato->delete();
        });

        $this->auditoria("Contrato eliminado [{$descripcion}] del colaborador #{$empleadoId}");

        $this->sincronizarEstadoColaborador(Empleado::findOrFail($empleadoId));

        return redirect()->route('sgrh.empleado.edit', $empleadoId)
            ->with('success', 'Contrato eliminado correctamente.');
    }

    /**
     * Regla: no puede haber colaboradores activos sin contrato activo. Se llama después de
     * cualquier cambio de estado de un contrato (alta, edición, renovación) para mantener
     * Empleado::estado sincronizado en ambos sentidos:
     * - Sin contrato activo → colaborador pasa a 'inactivo' (ej. al liquidar/vencer el
     *   contrato, o justo después de renovar, antes de crear el reemplazo).
     * - Con contrato activo → colaborador pasa a 'activo' (incluye el caso de recontratación:
     *   alguien 'retirado' que recibe un contrato nuevo vuelve a quedar activo).
     * No toca 'retirado' cuando ya no tiene contrato — evita pisar esa marca con 'inactivo'
     * sin necesidad, ya que ambas implican lo mismo (sin contrato vigente).
     */
    private function sincronizarEstadoColaborador(Empleado $empleado): void
    {
        $tieneContratoActivo = $empleado->contratoActivo()->exists();

        if ($tieneContratoActivo && $empleado->estado !== 'activo') {
            $empleado->update(['estado' => 'activo']);
            $this->auditoria("Colaborador #{$empleado->id} reactivado automáticamente (tiene un contrato activo)");
        } elseif (!$tieneContratoActivo && $empleado->estado === 'activo') {
            $empleado->update(['estado' => 'inactivo']);
            $this->auditoria("Colaborador #{$empleado->id} inactivado automáticamente (sin contrato activo)");
        }
    }

    private function registrarModificacion(Contrato $contrato, string $causal, ?string $observacion): void
    {
        ContratoModificacion::create([
            'contrato_id' => $contrato->id,
            'causal' => $causal,
            'observacion' => $observacion,
            'user_id' => Auth::id(),
        ]);
    }

    /**
     * Elimina un registro puntual del historial de modificaciones (ej. una prueba mal
     * registrada) — no afecta el contrato en sí, solo su bitácora de causales/observaciones.
     */
    public function destroyModificacion(ContratoModificacion $modificacion)
    {
        $contrato = $modificacion->contrato;
        $causal = $modificacion->causal;
        $modificacion->delete();

        $this->auditoria("Modificación ({$causal}) eliminada del historial del contrato #{$contrato->id}");

        return back()->with('success', 'Registro de modificación eliminado.');
    }

    private function validadoModificacion(Request $request): array
    {
        return $request->validate([
            'causal_modificacion' => 'required|in:' . implode(',', self::CAUSALES_MODIFICACION),
            // Si la causal es "Otra", la observación deja de ser opcional — es lo único que
            // explica qué pasó.
            'observacion_modificacion' => 'required_if:causal_modificacion,Otra|nullable|string',
        ]);
    }

    private function tieneOtroContratoActivo(int $empleadoId, string $estadoNuevo, ?int $exceptoId = null): bool
    {
        if ($estadoNuevo !== 'Activo') {
            return false;
        }

        return Contrato::where('empleado_id', $empleadoId)
            ->where('estado', 'Activo')
            ->when($exceptoId, fn($q) => $q->where('id', '!=', $exceptoId))
            ->exists();
    }

    private function validado(Request $request): array
    {
        // La fecha de vencimiento solo es obligatoria para contratos con término definido —
        // "Indefinido" es el único tipo del catálogo sin fecha de fin por definición.
        $tipoIndefinidoId = TipoContrato::where('nombre', 'Indefinido')->value('id');

        return $request->validate([
            'empleado_id' => 'required|exists:sgrh_empleados,id',
            'tipo_contrato_id' => 'required|exists:sgrh_tipos_contrato,id',
            // Único campo verdaderamente opcional aparte de cierre real/PDF/observaciones: por
            // decisión del usuario, todo lo demás del contrato debe quedar completo.
            'cargo_id' => 'required|exists:sgrh_cargos,id',
            'fecha_inicio' => 'required|date',
            // 'bail' evita que after_or_equal intente comparar contra un valor ausente cuando
            // la condición de abajo ya falló (si no, Laravel lanza un error de parseo de fecha real).
            'fecha_vencimiento' => [
                'bail',
                Rule::requiredIf(fn () => (int) $request->input('tipo_contrato_id') !== (int) $tipoIndefinidoId),
                'nullable',
                'date',
                'after_or_equal:fecha_inicio',
            ],
            'fecha_terminacion_real' => 'nullable|date',
            'estado' => 'required|in:' . implode(',', self::ESTADOS),
            'salario_contrato' => 'required|numeric|min:0',
            'observaciones' => 'nullable|string',
            // Enlace al gestor documental de la empresa (el PDF ya no se sube a S3 desde aquí).
            'documento_url' => 'nullable|url|max:2048',
        ]);
    }
}
