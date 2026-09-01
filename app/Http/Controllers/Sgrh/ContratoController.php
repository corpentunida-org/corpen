<?php

namespace App\Http\Controllers\Sgrh;

use App\Http\Controllers\AuditoriaController;
use App\Http\Controllers\Controller;
use App\Models\Sgrh\Cargo;
use App\Models\Sgrh\Contrato;
use App\Models\Sgrh\Empleado;
use App\Models\Sgrh\TipoContrato;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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

        if ($request->hasFile('documento')) {
            $validated['documento_path'] = $this->subirDocumento($request->file('documento'), $validated['empleado_id']);
        }
        unset($validated['documento']);

        $contrato = Contrato::create($validated);

        $this->auditoria("Contrato creado #{$contrato->id} para colaborador #{$contrato->empleado_id}");

        return redirect()->route('sgrh.empleado.edit', $contrato->empleado_id)
            ->with('success', 'Contrato registrado correctamente.');
    }

    public function edit(Contrato $contrato)
    {
        $contrato->load('empleado.tercero');

        return view('sgrh.contrato.edit', [
            'contrato' => $contrato,
            'tiposContrato' => TipoContrato::where('activo', true)->orderBy('nombre')->get(),
            'cargos' => Cargo::with('area')->where('activo', true)->orderBy('nombre')->get(),
            'estados' => self::ESTADOS,
        ]);
    }

    public function update(Request $request, Contrato $contrato)
    {
        $validated = $this->validado($request);

        if ($this->tieneOtroContratoActivo($validated['empleado_id'], $validated['estado'], $contrato->id)) {
            return back()->withInput()->with('error', 'Este colaborador ya tiene otro contrato activo.');
        }

        if ($validated['estado'] === 'Liquidado' && empty($validated['fecha_terminacion_real']) && empty($contrato->fecha_terminacion_real)) {
            $validated['fecha_terminacion_real'] = now()->format('Y-m-d');
        }

        if ($request->hasFile('documento')) {
            $validated['documento_path'] = $this->subirDocumento($request->file('documento'), $validated['empleado_id']);
        }
        unset($validated['documento']);

        $contrato->update($validated);

        $this->auditoria("Contrato actualizado #{$contrato->id} (colaborador #{$contrato->empleado_id})");

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

    public function verDocumento(Contrato $contrato)
    {
        return $this->responderDocumento($contrato, 'inline');
    }

    public function downloadDocumento(Contrato $contrato)
    {
        return $this->responderDocumento($contrato, 'attachment');
    }

    private function responderDocumento(Contrato $contrato, string $disposicion)
    {
        if (!$contrato->documento_path) {
            abort(404, 'Este contrato no tiene un documento adjunto.');
        }

        $nombreDescarga = 'contrato_' . $contrato->id . '.' . pathinfo($contrato->documento_path, PATHINFO_EXTENSION);

        // Mismo patrón que GdoEmpleadoController::verDocumento/downloadDocumento: URL temporal
        // de S3 (no se sirve el archivo a través de Laravel).
        $url = Storage::disk('s3')->temporaryUrl(
            $contrato->documento_path,
            now()->addMinutes(15),
            [
                'ResponseContentType' => 'application/pdf',
                'ResponseContentDisposition' => "{$disposicion}; filename=\"{$nombreDescarga}\"",
            ]
        );

        return redirect($url);
    }

    private function subirDocumento(UploadedFile $archivo, int $empleadoId): string
    {
        $nombre = 'DOC_' . time() . '_' . Str::random(5) . '.' . $archivo->getClientOriginalExtension();

        return $archivo->storeAs("sgrh/contratos/{$empleadoId}", $nombre, 's3');
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
        return $request->validate([
            'empleado_id' => 'required|exists:sgrh_empleados,id',
            'tipo_contrato_id' => 'required|exists:sgrh_tipos_contrato,id',
            'cargo_id' => 'nullable|exists:sgrh_cargos,id',
            'fecha_inicio' => 'required|date',
            'fecha_vencimiento' => 'nullable|date|after_or_equal:fecha_inicio',
            'fecha_terminacion_real' => 'nullable|date',
            'estado' => 'required|in:' . implode(',', self::ESTADOS),
            'salario_contrato' => 'nullable|numeric|min:0',
            'observaciones' => 'nullable|string',
            'documento' => 'nullable|file|mimes:pdf|max:5120',
        ]);
    }
}
