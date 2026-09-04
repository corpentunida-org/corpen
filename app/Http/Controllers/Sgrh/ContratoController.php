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

    private const CAUSALES_MODIFICACION = ['Cambio de cargo', 'Cambio de salario', 'Cambio de cargo y salario', 'Renovación', 'Otra'];

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

        // Se ordena por fecha_creacion_contrato (siempre presente) y no por fecha_inicio, que
        // ahora puede quedar sin definir en contratos Indefinido.
        $contratos = $query->latest('fecha_creacion_contrato')->paginate(20)->appends($request->query());
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

        // fecha_creacion_contrato siempre es la fecha del sistema al registrar — el formulario
        // ya la muestra deshabilitada, pero esto lo garantiza aunque alguien envíe otro valor
        // directamente. Solo fecha_inicio se digita al crear.
        $validated['fecha_creacion_contrato'] = now()->format('Y-m-d');

        if ($this->tieneOtroContratoActivo($validated['empleado_id'], $validated['estado'])) {
            return back()->withInput()
                ->with('error', 'Este colaborador ya tiene un contrato activo. Ciérralo o renuévalo antes de crear uno nuevo.');
        }

        if ($validated['estado'] === 'Liquidado' && empty($validated['fecha_terminacion_real'])) {
            $validated['fecha_terminacion_real'] = now()->format('Y-m-d');
        }

        // Transacción: el contrato y su evento de Creación tienen que quedar juntos o ninguno —
        // sin esto, si registrarModificacion() fallara después de crear el contrato, quedaría un
        // contrato sin ningún evento en su historial (modificaciones() vendría vacío), y la
        // ficha del colaborador asume que siempre hay al menos uno.
        $contrato = DB::transaction(function () use ($validated) {
            $contrato = Contrato::create($validated);

            // 'Creación' es el primer evento del historial — lleva su propia foto del contrato,
            // igual que cada modificación posterior, para poder verlo/imprimirlo tal como quedó
            // al registrarse.
            $this->registrarModificacion($contrato, 'Creación', null, $this->snapshotDe($contrato));

            return $contrato;
        });

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
        $validated = $this->validado($request, $contrato);
        $modificacion = $this->validadoModificacion($request);

        // fecha_inicio y fecha_creacion_contrato no se pueden modificar una vez creado el
        // contrato — el formulario las muestra deshabilitadas, pero esto lo garantiza aunque
        // alguien intente forzar otro valor enviando la petición directamente. fecha_inicio
        // puede ser null (contrato Indefinido sin fecha de inicio conocida al registrarse).
        $validated['fecha_inicio'] = $contrato->fecha_inicio?->format('Y-m-d');
        $validated['fecha_creacion_contrato'] = $contrato->fecha_creacion_contrato->format('Y-m-d');

        if ($this->tieneOtroContratoActivo($validated['empleado_id'], $validated['estado'], $contrato->id)) {
            return back()->withInput()->with('error', 'Este colaborador ya tiene otro contrato activo.');
        }

        if ($validated['estado'] === 'Liquidado' && empty($validated['fecha_terminacion_real']) && empty($contrato->fecha_terminacion_real)) {
            $validated['fecha_terminacion_real'] = now()->format('Y-m-d');
        }

        $contrato->update($validated);

        $this->auditoria("Contrato actualizado #{$contrato->id} (colaborador #{$contrato->empleado_id})");

        // Cada edición de contrato deja un registro propio en sgrh_contrato_modificaciones
        // (causal + observación + foto del contrato ya actualizado), aparte del log genérico de
        // Auditoria — es lo que permite luego consultar específicamente "por qué cambió este
        // contrato" y ver/imprimir cómo quedó vigente a partir de ese momento.
        $this->registrarModificacion(
            $contrato,
            $modificacion['causal_modificacion'],
            $modificacion['observacion_modificacion'] ?? null,
            $this->snapshotDe($contrato)
        );

        // El estado del colaborador nunca se toca a mano: sale de aquí. Liquidar un contrato lo
        // deja 'inactivo' salvo que se marque retiro definitivo, en cuyo caso queda 'retirado' —
        // sincronizarEstadoColaborador() ya sabe no pisar 'retirado' con 'inactivo' después.
        if ($contrato->estado === 'Liquidado' && $request->boolean('retiro_definitivo')) {
            $contrato->empleado->update(['estado' => 'retirado', 'fecha_retiro' => now()]);
            $this->auditoria("Colaborador #{$contrato->empleado_id} marcado como retirado definitivamente (contrato #{$contrato->id} liquidado)");
        }

        $this->sincronizarEstadoColaborador($contrato->empleado);

        return redirect()->route('sgrh.empleado.edit', $contrato->empleado_id)
            ->with('success', 'Contrato actualizado correctamente.');
    }

    /**
     * Una renovación NO es un contrato nuevo — es el mismo contrato continuado, con su
     * vencimiento extendido. Por eso no crea un registro en sgrh_contratos aparte: solo manda
     * al formulario de edición del mismo contrato con una fecha de vencimiento sugerida
     * (+1 año) y la causa de modificación preseleccionada en 'Renovación'; el administrador
     * confirma (o ajusta) y el guardado normal de update() deja el evento en su historial. Así
     * alguien con 12 años de renovaciones anuales sigue siendo UN solo contrato con 12 eventos
     * en su historial, no 12 contratos distintos en el listado.
     */
    public function renovar(Contrato $contrato)
    {
        if ($contrato->estado !== 'Activo') {
            return back()->with('error', 'Solo se puede renovar un contrato activo.');
        }

        // Igual criterio que el 'ancla' de validado(): si el vencimiento actual ya quedó en el
        // pasado (contrato vencido hace tiempo pero aún no marcado así), sugerir desde ahí+1año
        // daría una fecha que sigue en el pasado y la propia sugerencia fallaría la validación
        // "after:today" al confirmar sin tocarla. Se ancla desde hoy en ese caso.
        $baseSugerencia = $contrato->fecha_vencimiento?->isFuture() ? $contrato->fecha_vencimiento : now();
        $fechaVencimientoSugerida = $baseSugerencia->copy()->addYear();

        return redirect()->route('sgrh.contrato.edit', $contrato)
            ->with('fechaVencimientoSugerida', $fechaVencimientoSugerida->format('Y-m-d'))
            ->with('causalSugerida', 'Renovación')
            ->with('success', 'Confirma la nueva fecha de vencimiento para registrar la renovación.');
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
        $descripcion = "{$contrato->tipoContrato->nombre} (" . ($contrato->fecha_inicio?->format('d/m/Y') ?? 'sin fecha de inicio') . ')';

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
            // fecha_retiro se limpia aquí: si venía de 'retirado' (recontratación), no debe
            // quedar una fecha de retiro vieja colgada en un colaborador que ya volvió a estar
            // activo — antes vivía en el updateEstado() manual que se eliminó, ahora es este el
            // único lugar donde 'activo' se asigna.
            $empleado->update(['estado' => 'activo', 'fecha_retiro' => null]);
            $this->auditoria("Colaborador #{$empleado->id} reactivado automáticamente (tiene un contrato activo)");
        } elseif (!$tieneContratoActivo && $empleado->estado === 'activo') {
            $empleado->update(['estado' => 'inactivo']);
            $this->auditoria("Colaborador #{$empleado->id} inactivado automáticamente (sin contrato activo)");
        }
    }

    private function registrarModificacion(Contrato $contrato, string $causal, ?string $observacion, ?array $snapshot = null): void
    {
        ContratoModificacion::create([
            'contrato_id' => $contrato->id,
            'causal' => $causal,
            'observacion' => $observacion,
            'snapshot' => $snapshot,
            'user_id' => Auth::id(),
        ]);
    }

    /**
     * Foto del contrato en su estado actual — nombres ya resueltos (no solo los IDs), para que
     * el snapshot siga siendo correcto aunque después se renombre el cargo o el tipo de
     * contrato: lo que se ve al imprimir es lo que era cierto en ese momento, no lo que el
     * catálogo dice hoy.
     */
    private function snapshotDe(Contrato $contrato): array
    {
        // 'load' (no 'loadMissing'): si el cargo/tipo acaba de cambiar en este mismo request,
        // una relación ya cacheada en memoria seguiría apuntando al valor anterior aunque el
        // atributo *_id ya esté actualizado — hay que refrescarla siempre, no solo si falta.
        $contrato->load('tipoContrato', 'cargo.area', 'empleado.tercero');

        return [
            'tipo_contrato' => $contrato->tipoContrato->nombre,
            'cargo' => $contrato->cargo->nombre,
            'area' => $contrato->cargo->area->nombre ?? null,
            'fecha_creacion_contrato' => $contrato->fecha_creacion_contrato?->format('Y-m-d'),
            'fecha_inicio' => $contrato->fecha_inicio?->format('Y-m-d'),
            'fecha_vencimiento' => $contrato->fecha_vencimiento?->format('Y-m-d'),
            'estado' => $contrato->estado,
            'salario_contrato' => $contrato->salario_contrato,
            'documento_url' => $contrato->documento_url,
            'empleado_nombre' => $contrato->empleado->nombre_completo,
            'empleado_documento' => $contrato->empleado->tercero->cod_ter ?? null,
        ];
    }

    /**
     * Vista imprimible del contrato tal como quedó vigente a partir de este evento puntual del
     * historial (creación o una modificación específica) — no el estado actual del contrato,
     * salvo que sea justo el evento más reciente.
     */
    public function verModificacion(ContratoModificacion $modificacion)
    {
        $modificacion->load('contrato.empleado.tercero', 'contrato.modificaciones', 'contrato.tipoContrato', 'contrato.cargo.area', 'usuario');

        return view('sgrh.contrato.imprimir', [
            'modificacion' => $modificacion,
            'contrato' => $modificacion->contrato,
        ]);
    }

    /**
     * Vista imprimible del historial COMPLETO de contratos de un colaborador: cada contrato,
     * en orden cronológico (de la creación en adelante), con una línea de "qué cambió" por
     * evento (comparando el snapshot de cada evento contra el del evento anterior) — ej.
     * "Cambio tipo de contrato Término fijo por Indefinido" o "Actualización salario de
     * $1.000.000 a $1.500.000".
     */
    public function imprimirHistorialEmpleado(Empleado $empleado)
    {
        $empleado->load('tercero', 'contratos.tipoContrato', 'contratos.cargo.area', 'contratos.modificaciones.usuario');

        $historial = $empleado->contratos
            ->sortBy('fecha_creacion_contrato')
            ->values()
            ->map(function (Contrato $contrato) {
                $anterior = null;

                $eventos = $contrato->modificaciones->sortBy('created_at')->values()->map(function (ContratoModificacion $evento) use (&$anterior) {
                    $diferencias = ($anterior && $evento->snapshot)
                        ? $this->diferenciasEntre($anterior, $evento->snapshot, $evento->causal)
                        : [];

                    if ($evento->snapshot) {
                        $anterior = $evento->snapshot;
                    }

                    return ['modificacion' => $evento, 'diferencias' => $diferencias];
                });

                return ['contrato' => $contrato, 'eventos' => $eventos];
            });

        return view('sgrh.contrato.imprimir-historial', [
            'empleado' => $empleado,
            'historial' => $historial,
        ]);
    }

    /**
     * Compara dos snapshots consecutivos y arma frases legibles de qué cambió — solo para los
     * campos con valor distinto entre uno y otro.
     */
    private function diferenciasEntre(array $anterior, array $nuevo, string $causal): array
    {
        $etiquetas = [
            'tipo_contrato' => 'tipo de contrato',
            'cargo' => 'cargo',
            'estado' => 'estado',
            'salario_contrato' => 'salario',
            'fecha_vencimiento' => 'fecha de vencimiento',
            'documento_url' => 'documento firmado',
        ];

        $diferencias = [];
        foreach ($etiquetas as $clave => $etiqueta) {
            $antes = $anterior[$clave] ?? null;
            $despues = $nuevo[$clave] ?? null;
            if ((string) $antes === (string) $despues) {
                continue;
            }

            $antesTexto = $this->formatearValorCampo($clave, $antes);
            $despuesTexto = $this->formatearValorCampo($clave, $despues);

            $diferencias[] = match (true) {
                $clave === 'tipo_contrato' => "Cambio tipo de contrato {$antesTexto} por {$despuesTexto}",
                $clave === 'salario_contrato' => "Actualización salario de {$antesTexto} a {$despuesTexto}",
                $clave === 'fecha_vencimiento' && $causal === 'Renovación' => "Actualización fecha por renovación de {$antesTexto} a {$despuesTexto}",
                $clave === 'fecha_vencimiento' => "Actualización fecha de vencimiento de {$antesTexto} a {$despuesTexto}",
                $clave === 'documento_url' => 'Documento firmado actualizado',
                default => "Cambio de {$etiqueta} de {$antesTexto} a {$despuesTexto}",
            };
        }

        return $diferencias;
    }

    private function formatearValorCampo(string $campo, $valor): string
    {
        if ($valor === null || $valor === '') {
            return '—';
        }

        return match ($campo) {
            'salario_contrato' => '$' . number_format((float) $valor, 0, ',', '.'),
            'fecha_vencimiento' => \Illuminate\Support\Carbon::parse($valor)->format('d/m/Y'),
            default => (string) $valor,
        };
    }

    /**
     * Elimina un registro puntual del historial de modificaciones (ej. una prueba mal
     * registrada) — no afecta el contrato en sí, solo su bitácora de causales/observaciones.
     */
    public function destroyModificacion(ContratoModificacion $modificacion)
    {
        // El evento de Creación es el ancla del historial (de ahí sale el snapshot más antiguo
        // disponible) — no se borra suelto; si hace falta, se borra el contrato completo.
        if ($modificacion->causal === 'Creación') {
            return back()->with('error', 'El evento de creación no se puede eliminar por separado.');
        }

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

    private function validado(Request $request, ?Contrato $contrato = null): array
    {
        // La fecha de vencimiento solo es obligatoria para contratos con término definido —
        // "Indefinido" es el único tipo del catálogo sin fecha de fin por definición.
        $tipoIndefinidoId = TipoContrato::where('nombre', 'Indefinido')->value('id');

        // El límite superior de "dentro del próximo año" se cuenta desde la fecha más tardía
        // entre hoy y el vencimiento actual del contrato (si ya tiene uno futuro) — así una
        // renovación hecha días o semanas antes del vencimiento real (el caso típico) no queda
        // bloqueada por esta regla, sin dejar de detectar años mal digitados.
        $ancla = $contrato?->fecha_vencimiento?->isFuture() ? $contrato->fecha_vencimiento : now();

        return $request->validate([
            'empleado_id' => 'required|exists:sgrh_empleados,id',
            // Fecha en que se redactó/suscribió el contrato — independiente de fecha_inicio.
            'fecha_creacion_contrato' => 'required|date',
            'tipo_contrato_id' => 'required|exists:sgrh_tipos_contrato,id',
            'cargo_id' => 'required|exists:sgrh_cargos,id',
            // Igual que fecha_vencimiento: "Indefinido" es el único tipo que puede quedar sin
            // fecha de inicio conocida.
            'fecha_inicio' => [
                Rule::requiredIf(fn () => (int) $request->input('tipo_contrato_id') !== (int) $tipoIndefinidoId),
                'nullable',
                'date',
            ],
            // 'bail' evita que after/before intenten comparar contra un valor ausente cuando la
            // condición de abajo ya falló (si no, Laravel lanza un error de parseo de fecha real).
            // Rango "posterior a hoy y dentro del próximo año" solo aplica a contratos Activo —
            // evita errores de tipeo (ej. un año equivocado) al registrar o renovar un contrato
            // vigente, sin bloquear la edición de contratos ya cerrados (Vencido/Liquidado/
            // Renovado), cuya fecha de vencimiento es historia fija y puede estar en el pasado.
            'fecha_vencimiento' => [
                'bail',
                Rule::requiredIf(fn () => (int) $request->input('tipo_contrato_id') !== (int) $tipoIndefinidoId),
                'nullable',
                'date',
                // Siempre >= fecha_inicio cuando esta tiene valor, sin importar el estado — un
                // contrato no puede vencer antes de empezar. Va condicionada a filled() porque
                // fecha_inicio puede venir ausente (Indefinido) y after_or_equal contra un campo
                // sin valor revienta con un error de parseo real en vez de fallar limpio.
                Rule::when($request->filled('fecha_inicio'), 'after_or_equal:fecha_inicio'),
                // El rango "posterior a hoy y dentro del próximo año" sí es exclusivo de
                // contratos Activo — evita errores de tipeo (ej. un año equivocado) al registrar
                // o renovar uno vigente, sin bloquear la edición de contratos ya cerrados
                // (Vencido/Liquidado/Renovado), cuya fecha de vencimiento es historia fija y
                // puede estar en el pasado.
                Rule::when(
                    $request->input('estado') === 'Activo',
                    ['after:today', 'before_or_equal:' . $ancla->copy()->addYear()->format('Y-m-d')]
                ),
            ],
            'fecha_terminacion_real' => 'nullable|date',
            'estado' => 'required|in:' . implode(',', self::ESTADOS),
            'salario_contrato' => 'required|numeric|min:0',
            // Enlace al gestor documental de la empresa (el PDF ya no se sube a S3 desde aquí).
            'documento_url' => 'nullable|url|max:2048',
        ]);
    }
}
