<?php

namespace App\Http\Controllers\Sgrh;

use App\Http\Controllers\AuditoriaController;
use App\Http\Controllers\Controller;
use App\Http\Requests\Sgrh\StoreEmpleadoRequest;
use App\Models\Maestras\EstadoCivil;
use App\Models\Maestras\MaeDepartamento;
use App\Models\Maestras\MaeTerceros;
use App\Models\Maestras\MaeTipo;
use App\Models\Maestras\Parentesco;
use App\Models\Maestras\TipoDocumento;
use App\Models\Sgrh\Arl;
use App\Models\Sgrh\Empleado;
use App\Models\Sgrh\Eps;
use App\Models\Sgrh\FondoPension;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

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
        // Se llama en cada búsqueda/alta/listado — sin caché costaba ~690ms por ser una
        // consulta remota (AWS RDS). El id de "Empleado" en el catálogo prácticamente nunca
        // cambia, así que se cachea igual que terceroCatalogos().
        return Cache::remember('sgrh.tipo_empleado_id', now()->addDays(7), function () {
            return MaeTipo::where('nombre', 'Empleado')->value('id');
        });
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
        // MaeTerceros no tiene updated_at — fec_act cumple ese rol (ver fechaDesactualizada()).
        $fechaLimiteActualizacion = now()->subYears(1)->format('Y-m-d');

        return view('sgrh.empleado.index', compact('empleados', 'tipoEmpleadoId', 'fechaLimiteActualizacion'));
    }

    /**
     * Formulario de alta: busca primero el tercero, luego completa los datos propios.
     */
    public function create()
    {
        return view('sgrh.empleado.create', [
            'listaEps' => Eps::where('activo', true)->orderBy('nombre')->pluck('nombre'),
            'listaArl' => Arl::where('activo', true)->orderBy('nombre')->pluck('nombre'),
            'listaFondosPension' => FondoPension::where('activo', true)->orderBy('nombre')->pluck('nombre'),
        ]);
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

        // Búsqueda por nombre vía FULLTEXT (índice ft_mae_terceros_nombres) — medido: ~4.3x más
        // rápido que el LIKE '%...%' que se usaba antes (que no puede usar ningún índice por
        // el comodín al inicio). cod_ter (numérico) sigue por LIKE, FULLTEXT no aplica ahí.
        // Cada palabra se busca como prefijo ("palabra*"); se limpian caracteres especiales de
        // sintaxis booleana para que un usuario no pueda romper la consulta sin querer.
        $terminoBooleano = collect(preg_split('/\s+/', $q))
            ->filter()
            ->map(fn($palabra) => '+' . preg_replace('/[+\-><()~*"@]/', '', $palabra) . '*')
            ->filter(fn($palabra) => $palabra !== '+*')
            ->implode(' ');

        // 'MaeTerceros.cod_ter' calificado a propósito: tras el leftJoin con sgrh_empleados
        // (que también tiene columna cod_ter), un 'cod_ter' sin calificar sería ambiguo.
        $query = DB::table('MaeTerceros')->where('MaeTerceros.cod_ter', 'like', "%{$q}%");

        if ($terminoBooleano !== '') {
            $query->orWhereRaw(
                'MATCH(nom1, nom2, apl1, apl2, nom_ter) AGAINST(? IN BOOLEAN MODE)',
                [$terminoBooleano]
            );
        }

        // LEFT JOIN con sgrh_empleados en la misma consulta (en vez de una segunda consulta
        // aparte para "ya_registrado"): cada consulta paga ~130-150ms de latencia fija contra
        // la base de datos remota, sin importar qué tan simple sea, así que combinar consultas
        // ahorra más que optimizar cada una por separado.
        $terceros = $query
            ->leftJoin('sgrh_empleados', 'sgrh_empleados.cod_ter', '=', 'MaeTerceros.cod_ter')
            ->limit(10)
            ->get([
                'MaeTerceros.cod_ter', 'MaeTerceros.nom1', 'MaeTerceros.nom2', 'MaeTerceros.apl1',
                'MaeTerceros.apl2', 'MaeTerceros.nom_ter', 'MaeTerceros.email', 'MaeTerceros.cel',
                'MaeTerceros.tip_prv', 'MaeTerceros.fec_act', 'sgrh_empleados.id as empleado_id',
            ]);

        if ($terceros->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'No se encontró ningún tercero que coincida con esa búsqueda.',
            ], 404);
        }

        $tipoEmpleadoId = $this->tipoEmpleadoId();

        $data = $terceros->map(function ($t) use ($tipoEmpleadoId) {
            $nombre = trim("{$t->nom1} {$t->nom2} {$t->apl1} {$t->apl2}");

            return [
                'cod_ter' => $t->cod_ter,
                'nombre_completo' => $nombre !== '' ? $nombre : ($t->nom_ter ?? ''),
                'email' => $t->email,
                'celular' => $t->cel,
                'ya_registrado' => $t->empleado_id !== null,
                'clasificado_empleado' => $tipoEmpleadoId !== null && (int) $t->tip_prv === $tipoEmpleadoId,
                'fecha_actualizacion' => $t->fec_act ? substr($t->fec_act, 0, 10) : null,
                'desactualizado' => $this->fechaDesactualizada($t->fec_act),
            ];
        });

        return response()->json(['status' => 'success', 'data' => $data]);
    }

    /**
     * Registra un nuevo colaborador enlazado a un tercero ya existente.
     */
    public function store(StoreEmpleadoRequest $request)
    {
        $datos = $request->validated();

        if (!empty($datos['contacto_emergencia_nombre'])) {
            $datos['contacto_emergencia_nombre'] = mb_strtoupper($datos['contacto_emergencia_nombre'], 'UTF-8');
        }

        $empleado = Empleado::create($datos);

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
                'dv' => 'hash', 'fec_expcc' => 'calendar', 'lugar_expcc' => 'map-pin', 'lugar_naci' => 'map-pin',
                'digito_v' => 'hash', 'razon_soc' => 'briefcase', 'nom_conyug' => 'user-plus',
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
                'fec_expcc' => 'Fecha de expedición', 'digito_v' => 'Dígito de verificación (alterno)', 'razon_soc' => 'Razón social',
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
                // tip_prv ya no es editable aquí: se muestra fijo junto a cod_ter/nom_ter y
                // se actualiza automáticamente desde "tipo_ter" (Información Personal).
                'Identificación' => [],
                'Información Personal' => [
                    // digito_v, razon_soc y raz ocultos a pedido: son duplicados de dv/nom_ter
                    // (campos de persona jurídica) que no aplican al caso de uso de RR. HH.
                    // lugar_naci y lugar_expcc se movieron aquí desde Ubicación, junto a
                    // tdoc/dv/fec_expcc, según el orden pedido. Los campos del cónyuge se
                    // movieron a su propia sección "Información Cónyuge" (ver abajo).
                    'estado', 'apl1', 'apl2', 'nom1', 'nom2', 'sexo', 'fec_nac', 'est_civil',
                    'tipo_ter', 'tip_pers', 'tdoc', 'dv', 'fec_expcc', 'lugar_expcc', 'lugar_naci',
                    'fec_falle', 'contacto', 'cargo',
                ],
                'Información Cónyuge' => [
                    'id_conyuge', 'nom_conyug', 'mail_conyu', 'parentesco', 'num_hijos', 'cont_tel',
                ],
                'Ubicación' => [
                    // 'ciudad' oculto: texto libre truncado, redundante con 'mun' (Municipio).
                    // 'cod_depa' oculto: duplica 'dpto' (mismo código DANE, llenados de forma
                    // inconsistente entre sí) — se fusionan, dpto escribe en los dos al guardar.
                    // 'cod_pais' oculto: duplica 'pais' con un código numérico de esquema
                    // desconocido (no es ISO) — se deja intacto en BD, no se puede sincronizar
                    // con seguridad sin saber qué significa cada número.
                    'dir', 'dir1', 'dir2', 'dir_comer', 'ciu_comer', 'dpto', 'mun',
                    'pais', 'cod_postal', 'barrio',
                ],
                'Contacto' => ['tel', 'tel1', 'tel2', 'cel', 'fax1', 'email'],
            ],
            // Se guardan siempre en mayúsculas (nombres, apellidos, cónyuge).
            'uppercaseFields' => ['apl1', 'apl2', 'nom1', 'nom2', 'nom_conyug', 'barrio'],
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
        // La vista de solo lectura no necesita el listado completo de municipios (solo se usa
        // para el filtro en cascada del formulario de edición) — se resuelve únicamente el
        // nombre del municipio actual, evitando cargar/enviar las 1.125 filas sin necesidad.
        $catalogos = $this->terceroCatalogos();
        $municipioActual = $catalogos['municipios']->firstWhere('id', (int) $tercero->mun);
        $catalogos['municipios'] = $municipioActual ? collect([$municipioActual]) : collect();

        return view('sgrh.empleado.tercero-show', array_merge(
            ['tercero' => $tercero, 'desactualizado' => $this->fechaDesactualizada($tercero->fec_act)],
            $catalogos,
            $this->terceroFieldMeta()
        ));
    }

    /**
     * MaeTerceros no tiene updated_at de Laravel — fec_act cumple ese rol (ver
     * updateTercero(), que lo refresca en cada guardado desde SGRH). Sin fecha registrada
     * cuenta como desactualizado también (peor que 1 año, en realidad).
     */
    private function fechaDesactualizada(?string $fecAct): bool
    {
        if (!$fecAct) {
            return true;
        }

        return $fecAct < now()->subYears(1)->format('Y-m-d');
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
        return view('sgrh.empleado.tercero-edit', array_merge(
            ['tercero' => $tercero, 'desactualizado' => $this->fechaDesactualizada($tercero->fec_act)],
            $this->terceroCatalogos(),
            $this->terceroFieldMeta()
        ));
    }

    /**
     * Catálogos usados por las vistas show/edit del tercero. Casi no cambian (tipos de
     * documento, departamentos, municipios...) pero se volvían a consultar completos en cada
     * carga de la página — medido: ~2.3 segundos contra la base de datos remota (AWS RDS),
     * solo municipios costaba 785ms de esos. Se cachean 7 días. Se usa `DB::table()` en vez
     * de los modelos Eloquent para tipos/municipios/países: hidratar cientos de instancias de
     * modelo (con sus traits/casts) es notablemente más lento de serializar/deserializar en
     * caché que las filas planas (stdClass) de una consulta de query builder — medido: ~640ms
     * vs ~300ms para leer el mismo catálogo ya cacheado.
     *
     * Si algún día se edita uno de estos catálogos desde su propia pantalla, hay que limpiar
     * la caché (`Cache::forget('sgrh.catalogos.tercero')`) o esperar a que expire el TTL.
     */
    private function terceroCatalogos(): array
    {
        return Cache::remember('sgrh.catalogos.tercero', now()->addDays(7), function () {
            return [
                'tipos' => DB::table('MaeTipos')->orderBy('nombre')->get(['id', 'nombre']),
                'tiposDocumento' => TipoDocumento::orderBy('codigo')->pluck('nombre', 'codigo'),
                'estadosCiviles' => EstadoCivil::orderBy('codigo')->pluck('nombre', 'codigo'),
                'parentescos' => Parentesco::orderBy('name')->pluck('name', 'code'),
                'departamentos' => MaeDepartamento::orderBy('nombre')->pluck('nombre', 'codigo_Dane'),
                // Se pasa completo (no pluck) porque el select de municipio se filtra en el
                // navegador según el departamento elegido (id_departamento de cada uno).
                'municipios' => DB::table('MaeMunicipios')->orderBy('nombre')->get(['id', 'nombre', 'id_departamento']),
                'paises' => DB::table('paises')->orderBy('nombre')->get(['codigo_iso', 'nombre']),
            ];
        });
    }

    /**
     * Actualiza únicamente los campos de las secciones mostradas en editTercero().
     */
    public function updateTercero(Request $request, MaeTerceros $tercero)
    {
        $validated = $request->validate([
            // Identificación: nom_ter y tip_prv no se reciben del formulario, se recalculan
            // abajo (nom_ter desde nom1/nom2/apl1/apl2, tip_prv como copia de tipo_ter).

            // Información Personal
            'estado' => 'nullable|string|max:10',
            'apl1' => 'nullable|string|max:100',
            'apl2' => 'nullable|string|max:100',
            'nom1' => 'nullable|string|max:255',
            'nom2' => 'nullable|string|max:255',
            'sexo' => 'nullable|string|max:10',
            'fec_nac' => 'nullable|date',
            'est_civil' => 'nullable|string|max:10',
            // tipo_ter ahora es el selector real (catálogo MaeTipos); tip_prv lo copia.
            'tipo_ter' => 'nullable|integer',
            'tip_pers' => 'nullable|string|max:10',
            'tdoc' => 'nullable|string|max:10',
            // Solo aplica (y es obligatorio) cuando el tipo de documento es NIT (31).
            'dv' => 'nullable|required_if:tdoc,31|string|max:10',
            'fec_expcc' => 'nullable|date',
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
            // dpto = MaeDepartamentos.codigo_Dane, mun = MaeMunicipios.id (verificado contra
            // datos reales de MaeTerceros antes de construir el select en cascada).
            'dpto' => 'nullable|integer',
            'mun' => 'nullable|integer',
            'pais' => 'nullable|string|max:100',
            'cod_postal' => 'nullable|string|max:10',
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

        // Defensa adicional: si '' llega hasta aquí como cadena vacía (en vez de null) para
        // una columna DATETIME (fec_nac, fec_falle), MySQL rechaza la consulta completa con
        // un QueryException — y como todos los campos van en el mismo UPDATE, ESO tumbaba el
        // guardado de TODO el formulario, no solo de la fecha. Se normaliza antes de guardar.
        $validated = array_map(fn($v) => $v === '' ? null : $v, $validated);

        // Nombres, apellidos, cónyuge y barrio siempre en mayúsculas.
        foreach (['apl1', 'apl2', 'nom1', 'nom2', 'nom_conyug', 'barrio'] as $campo) {
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

        // tip_prv tampoco es editable directamente: se mantiene siempre igual a tipo_ter,
        // que es ahora el selector real (catálogo MaeTipos) en Información Personal.
        $validated['tip_prv'] = $validated['tipo_ter'] ?? null;

        // cod_depa duplica dpto (mismo código DANE) — no se muestra por separado en el
        // formulario, pero se mantiene sincronizado para no dejarlo desactualizado.
        $validated['cod_depa'] = $validated['dpto'] ?? null;

        // MaeTerceros no tiene updated_at de Laravel — fec_act cumple ese rol. Se refresca en
        // cada guardado desde SGRH para que la alerta de "más de 1 año sin actualizar" se
        // resuelva sola la próxima vez que se consulte.
        $validated['fec_act'] = now()->format('Y-m-d');

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
