<?php

namespace App\Http\Controllers\Sgrh;

use App\Http\Controllers\AuditoriaController;
use App\Http\Controllers\Controller;
use App\Http\Requests\Sgrh\StoreEmpleadoRequest;
use App\Models\Maestras\EstadoCivil;
use App\Models\Maestras\MaeTerceros;
use App\Models\Maestras\MaeTipo;
use App\Models\Maestras\TipoDocumento;
use App\Models\Sgrh\Empleado;
use Illuminate\Http\Request;

class EmpleadoController extends Controller
{
    private function auditoria($accion)
    {
        app(AuditoriaController::class)->create($accion, 'SGRH');
    }

    /**
     * Id del catálogo MaeTipos cuyo nombre es "Empleado" — usado para comparar/actualizar
     * MaeTerceros.tip_prv sin hardcodear el id numérico.
     */
    private function tipoEmpleadoId(): ?int
    {
        return MaeTipo::where('nombre', 'Empleado')->value('id');
    }

    /**
     * Lista todos los colaboradores, con filtro de búsqueda por nombre/código de tercero.
     */
    public function index(Request $request)
    {
        $query = Empleado::with('tercero');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('cod_ter', 'like', "%{$search}%")
                    ->orWhereHas('tercero', function ($tq) use ($search) {
                        $tq->where('nom1', 'like', "%{$search}%")
                            ->orWhere('nom2', 'like', "%{$search}%")
                            ->orWhere('apl1', 'like', "%{$search}%")
                            ->orWhere('apl2', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $empleados = $query->latest('id')->paginate(20)->appends($request->query());
        $tipoEmpleadoId = $this->tipoEmpleadoId();

        return view('sgrh.empleado.index', compact('empleados', 'tipoEmpleadoId'));
    }

    /**
     * Formulario de alta: busca primero el tercero, luego completa los datos propios.
     */
    public function create()
    {
        return view('sgrh.empleado.create');
    }

    /**
     * Busca terceros por nombre o por cod_ter para identificarlos antes de registrarlos
     * como colaborador. Devuelve varias coincidencias (el usuario elige una en el formulario
     * de alta), no un único registro exacto.
     */
    public function buscarTercero(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        if ($q === '') {
            return response()->json([
                'status' => 'error',
                'message' => 'Escribe un nombre o una cédula para buscar.',
            ], 422);
        }

        $terceros = MaeTerceros::where(function ($query) use ($q) {
                $query->where('cod_ter', 'like', "%{$q}%")
                    ->orWhere('nom1', 'like', "%{$q}%")
                    ->orWhere('nom2', 'like', "%{$q}%")
                    ->orWhere('apl1', 'like', "%{$q}%")
                    ->orWhere('apl2', 'like', "%{$q}%")
                    ->orWhere('nom_ter', 'like', "%{$q}%");
            })
            ->limit(10)
            ->get(['cod_ter', 'nom1', 'nom2', 'apl1', 'apl2', 'nom_ter', 'email', 'cel', 'tip_prv']);

        if ($terceros->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'No se encontró ningún tercero que coincida con esa búsqueda.',
            ], 404);
        }

        $yaRegistrados = Empleado::whereIn('cod_ter', $terceros->pluck('cod_ter'))->pluck('cod_ter')->all();
        $tipoEmpleadoId = $this->tipoEmpleadoId();

        $data = $terceros->map(function ($t) use ($yaRegistrados, $tipoEmpleadoId) {
            $nombre = trim("{$t->nom1} {$t->nom2} {$t->apl1} {$t->apl2}");

            return [
                'cod_ter' => $t->cod_ter,
                'nombre_completo' => $nombre !== '' ? $nombre : ($t->nom_ter ?? ''),
                'email' => $t->email,
                'celular' => $t->cel,
                'ya_registrado' => in_array($t->cod_ter, $yaRegistrados),
                'clasificado_empleado' => $tipoEmpleadoId !== null && (int) $t->tip_prv === $tipoEmpleadoId,
            ];
        });

        return response()->json(['status' => 'success', 'data' => $data]);
    }

    /**
     * Registra un nuevo colaborador enlazado a un tercero ya existente.
     */
    public function store(StoreEmpleadoRequest $request)
    {
        $empleado = Empleado::create($request->validated());

        $this->auditoria("Alta de colaborador #{$empleado->id} (cod_ter {$empleado->cod_ter})");

        // Opt-in explícito: solo se toca MaeTerceros.tip_prv si el usuario marcó la casilla
        // en el formulario. Nunca se sobrescribe en automático (podría borrar otra
        // clasificación existente, ej. "Pastor", porque tip_prv es de selección única).
        if ($request->boolean('marcar_tip_prv')) {
            $tipoEmpleadoId = $this->tipoEmpleadoId();

            if ($tipoEmpleadoId !== null) {
                MaeTerceros::where('cod_ter', $empleado->cod_ter)->update(['tip_prv' => $tipoEmpleadoId]);
                $this->auditoria("Clasificación 'Empleado' aplicada en MaeTerceros para cod_ter {$empleado->cod_ter} (marcada explícitamente en el alta)");
            }
        }

        return redirect()->route('sgrh.empleado.index')
            ->with('success', 'Colaborador registrado correctamente.');
    }

    /**
     * Metadatos compartidos entre la vista de solo lectura y la de edición del tercero
     * (iconos, etiquetas legibles, agrupación en secciones). Viven en un solo lugar para que
     * las dos vistas no se desincronicen entre sí con el tiempo.
     */
    private function terceroFieldMeta(): array
    {
        return [
            'fieldIcons' => [
                'cod_ter' => 'hash', 'tip_prv' => 'award', 'nom_ter' => 'user', 'estado' => 'activity',
                'apl1' => 'user-check', 'apl2' => 'user-check', 'nom1' => 'user', 'nom2' => 'user',
                'sexo' => 'users', 'fec_nac' => 'calendar', 'est_civil' => 'heart', 'tdoc' => 'file-text',
                'dv' => 'hash', 'digito_v' => 'hash', 'razon_soc' => 'briefcase', 'nom_conyug' => 'user-plus',
                'id_conyuge' => 'hash', 'parentesco' => 'link', 'mail_conyu' => 'mail', 'num_hijos' => 'users',
                'fec_falle' => 'activity', 'contacto' => 'phone', 'cont_tel' => 'phone', 'cargo' => 'briefcase',
                'dir' => 'map-pin', 'ciu_comer' => 'map', 'ciudad' => 'map', 'dpto' => 'map', 'pais' => 'globe',
                'tel' => 'phone', 'cel' => 'smartphone', 'email' => 'mail',
            ],
            // Etiquetas legibles para RR. HH. — los nombres de columna (nom1, apl1, dv...)
            // vienen de la tabla legada MaeTerceros y no son autoexplicativos.
            'fieldLabels' => [
                'tip_prv' => 'Tipo de tercero', 'estado' => 'Estado', 'apl1' => 'Primer apellido',
                'apl2' => 'Segundo apellido', 'nom1' => 'Primer nombre', 'nom2' => 'Segundo nombre',
                'sexo' => 'Sexo', 'fec_nac' => 'Fecha de nacimiento', 'est_civil' => 'Estado civil',
                'tipo_ter' => 'Tipo (código interno)', 'tip_pers' => 'Tipo de persona',
                'tdoc' => 'Tipo de documento', 'dv' => 'Dígito de verificación',
                'digito_v' => 'Dígito de verificación (alterno)', 'razon_soc' => 'Razón social',
                'raz' => 'Razón social (alterna)', 'nom_conyug' => 'Nombre del cónyuge',
                'id_conyuge' => 'Cédula del cónyuge', 'parentesco' => 'Parentesco',
                'mail_conyu' => 'Correo del cónyuge', 'num_hijos' => 'Número de hijos',
                'fec_falle' => 'Fecha de fallecimiento', 'contacto' => 'Contacto',
                'cont_tel' => 'Teléfono de contacto', 'cargo' => 'Cargo', 'dir' => 'Dirección principal',
                'dir1' => 'Dirección alterna 1', 'dir2' => 'Dirección alterna 2',
                'dir_comer' => 'Dirección comercial', 'ciu_comer' => 'Ciudad comercial', 'ciudad' => 'Ciudad',
                'dpto' => 'Departamento', 'mun' => 'Municipio', 'pais' => 'País',
                'cod_postal' => 'Código postal', 'cod_pais' => 'Código de país',
                'cod_depa' => 'Código de departamento', 'barrio' => 'Barrio',
                'lugar_naci' => 'Lugar de nacimiento', 'lugar_expcc' => 'Lugar de expedición de cédula',
                'tel' => 'Teléfono fijo', 'tel1' => 'Teléfono alterno 1', 'tel2' => 'Teléfono alterno 2',
                'cel' => 'Celular', 'fax1' => 'Fax', 'email' => 'Correo electrónico',
            ],
            // Solo las secciones relevantes para RR. HH. — se omiten Iglesia/Financiera/Comercial/
            // Tributaria/Otros del formulario genérico de Maestras/Terceros a propósito.
            'groups' => [
                'Identificación' => ['tip_prv'],
                'Información Personal' => [
                    'estado', 'apl1', 'apl2', 'nom1', 'nom2', 'sexo', 'fec_nac', 'est_civil',
                    'tipo_ter', 'tip_pers', 'tdoc', 'dv', 'digito_v', 'razon_soc', 'raz',
                    'nom_conyug', 'id_conyuge', 'parentesco', 'mail_conyu', 'num_hijos',
                    'fec_falle', 'contacto', 'cont_tel', 'cargo',
                ],
                'Ubicación' => [
                    'dir', 'dir1', 'dir2', 'dir_comer', 'ciu_comer', 'ciudad', 'dpto', 'mun',
                    'pais', 'cod_postal', 'cod_pais', 'cod_depa', 'barrio', 'lugar_naci', 'lugar_expcc',
                ],
                'Contacto' => ['tel', 'tel1', 'tel2', 'cel', 'fax1', 'email'],
            ],
            // Se guardan siempre en mayúsculas (nombres, apellidos, cónyuge).
            'uppercaseFields' => ['apl1', 'apl2', 'nom1', 'nom2', 'nom_conyug'],
            'tdocPredeterminado' => '13',
            // '42' y '43' NO son tipos de documento: son indicadores de responsabilidad
            // tributaria DIAN (facturación electrónica / IVA) cargados por error en tdoc.
            'tdocCodigosInvalidosConocidos' => [
                '42' => 'Obligado a facturar electrónicamente (no es un tipo de documento — dato a corregir)',
                '43' => 'No responsable de IVA (no es un tipo de documento — dato a corregir)',
            ],
        ];
    }

    /**
     * Vista de solo lectura del tercero — para usuarios con permiso `sgrh.tercero.show`
     * pero sin `sgrh.tercero.edit`. Reutiliza el mismo partial de campos que editTercero(),
     * solo que sin formulario ni inputs habilitados.
     */
    public function showTercero(MaeTerceros $tercero)
    {
        $tipos = MaeTipo::all();
        $tiposDocumento = TipoDocumento::orderBy('codigo')->pluck('nombre', 'codigo');
        $estadosCiviles = EstadoCivil::orderBy('codigo')->pluck('nombre', 'codigo');

        return view('sgrh.empleado.tercero-show', array_merge(
            compact('tercero', 'tipos', 'tiposDocumento', 'estadosCiviles'),
            $this->terceroFieldMeta()
        ));
    }

    /**
     * Formulario para consultar/corregir los datos personales del tercero, acotado a las
     * secciones relevantes para RR. HH. (Identificación, Información Personal, Ubicación,
     * Contacto). No reemplaza al formulario completo de Maestras/Terceros — ese sigue siendo
     * el editor genérico usado por Cartera, Contabilidad, Inventario, etc. con sus secciones
     * Financiera/Comercial/Tributaria, que no aplican aquí.
     */
    public function editTercero(MaeTerceros $tercero)
    {
        $tipos = MaeTipo::all();
        $tiposDocumento = TipoDocumento::orderBy('codigo')->pluck('nombre', 'codigo');
        $estadosCiviles = EstadoCivil::orderBy('codigo')->pluck('nombre', 'codigo');

        return view('sgrh.empleado.tercero-edit', array_merge(
            compact('tercero', 'tipos', 'tiposDocumento', 'estadosCiviles'),
            $this->terceroFieldMeta()
        ));
    }

    /**
     * Actualiza únicamente los campos de las secciones mostradas en editTercero().
     */
    public function updateTercero(Request $request, MaeTerceros $tercero)
    {
        $validated = $request->validate([
            // Identificación (nom_ter no se recibe del formulario: se recalcula abajo)
            'tip_prv' => 'nullable|integer',

            // Información Personal
            'estado' => 'nullable|string|max:10',
            'apl1' => 'nullable|string|max:100',
            'apl2' => 'nullable|string|max:100',
            'nom1' => 'nullable|string|max:255',
            'nom2' => 'nullable|string|max:255',
            'sexo' => 'nullable|string|max:10',
            'fec_nac' => 'nullable|date',
            'est_civil' => 'nullable|string|max:10',
            'tipo_ter' => 'nullable|string|max:10',
            'tip_pers' => 'nullable|string|max:10',
            'tdoc' => 'nullable|string|max:10',
            'dv' => 'nullable|string|max:10',
            'digito_v' => 'nullable|string|max:255',
            'razon_soc' => 'nullable|string|max:10',
            'raz' => 'nullable|string|max:10',
            'nom_conyug' => 'nullable|string',
            'id_conyuge' => 'nullable|integer',
            'parentesco' => 'nullable|string|max:10',
            'mail_conyu' => 'nullable|email|max:100',
            'num_hijos' => 'nullable|string|max:10',
            'fec_falle' => 'nullable|date',
            'contacto' => 'nullable|string',
            'cont_tel' => 'nullable|string|max:10',
            'cargo' => 'nullable|string|max:100',

            // Ubicación
            'dir' => 'nullable|string|max:255',
            'dir1' => 'nullable|string|max:255',
            'dir2' => 'nullable|string|max:255',
            'dir_comer' => 'nullable|string|max:255',
            'ciu_comer' => 'nullable|string|max:10',
            'ciudad' => 'nullable|string|max:10',
            'dpto' => 'nullable|string|max:50',
            'mun' => 'nullable|string|max:10',
            'pais' => 'nullable|string|max:100',
            'cod_postal' => 'nullable|string|max:10',
            'cod_pais' => 'nullable|string|max:255',
            'cod_depa' => 'nullable|string|max:255',
            'barrio' => 'nullable|string|max:255',
            'lugar_naci' => 'nullable|string',
            'lugar_expcc' => 'nullable|string',

            // Contacto
            'tel' => 'nullable|string|max:255',
            'tel1' => 'nullable|string|max:255',
            'tel2' => 'nullable|string|max:255',
            'cel' => 'nullable|string|max:100',
            'fax1' => 'nullable|string|max:10',
            'email' => 'nullable|email|max:255',
        ]);

        // Nombres, apellidos y cónyuge siempre en mayúsculas.
        foreach (['apl1', 'apl2', 'nom1', 'nom2', 'nom_conyug'] as $campo) {
            if (array_key_exists($campo, $validated) && $validated[$campo] !== null) {
                $validated[$campo] = mb_strtoupper($validated[$campo], 'UTF-8');
            }
        }

        // nom_ter no es editable directamente: se recalcula a partir de nom1/nom2/apl1/apl2
        // (ya en mayúsculas) para que quede siempre coherente con esos campos.
        $validated['nom_ter'] = trim(implode(' ', array_filter([
            $validated['nom1'] ?? $tercero->nom1,
            $validated['nom2'] ?? $tercero->nom2,
            $validated['apl1'] ?? $tercero->apl1,
            $validated['apl2'] ?? $tercero->apl2,
        ])));

        $tercero->update($validated);

        $this->auditoria("Actualización de datos personales del tercero cod_ter {$tercero->cod_ter} desde el módulo SGRH");

        return redirect()->back()->with('success', 'Datos del tercero actualizados correctamente.');
    }

    /**
     * Cambia el estado de un colaborador (activo, inactivo, retirado).
     */
    public function updateEstado(Request $request, Empleado $empleado)
    {
        $validated = $request->validate([
            'estado' => 'required|in:activo,inactivo,retirado',
        ]);

        $estadoAnterior = $empleado->estado;
        $empleado->estado = $validated['estado'];
        $empleado->fecha_retiro = $validated['estado'] === 'retirado'
            ? ($empleado->fecha_retiro ?? now())
            : null;
        $empleado->save();

        $this->auditoria("Cambio de estado del empleado #{$empleado->id} (cod_ter {$empleado->cod_ter}) de '{$estadoAnterior}' a '{$empleado->estado}'");

        return redirect()->route('sgrh.empleado.index')
            ->with('success', 'El estado del colaborador se actualizó correctamente.');
    }
}
