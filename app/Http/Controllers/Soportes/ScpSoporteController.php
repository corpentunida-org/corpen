<?php

namespace App\Http\Controllers\Soportes;

use App\Http\Controllers\Controller;
use App\Models\Creditos\LineaCredito;
use App\Models\Soportes\ScpCategoria;
use App\Models\Soportes\ScpEstado;
use App\Models\Soportes\ScpObservacion;
use App\Models\Soportes\ScpPrioridad;
use App\Models\Soportes\ScpSoporte;
use App\Models\Soportes\ScpSubTipo;
use App\Models\Soportes\ScpTipo;
use App\Models\Soportes\ScpTipoObservacion;
use App\Models\Soportes\ScpUsuario;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

use App\Models\Cinco\Terceros;
use Illuminate\Support\Facades\Cache;

use Illuminate\Support\Facades\Mail;
use App\Mail\SoporteEscaladoMail;

class ScpSoporteController extends Controller
{
    /**
     * Puede ver/gestionar ESTE soporte puntual: quien lo creó, a quien está asignado, o quien
     * tiene un permiso "de bandera" que ve cualquiera (agente comodín, ver todos, o
     * administrador — este último ya ve Parámetros/Estadísticas, no tendría sentido que no
     * pudiera ver un ticket individual). Mismo criterio que ya usaba SOLO la vista (Blade) para
     * mostrar u ocultar botones — antes show()/edit()/update()/las observaciones no lo exigían
     * de verdad: cualquiera con acceso al módulo podía abrir cualquier ticket por URL.
     */
    private function autorizarVerSoporte(ScpSoporte $soporte): void
    {
        $user = Auth::user();
        $esCreador = $soporte->id_users === $user->id;
        $esAsignado = optional($soporte->scpUsuarioAsignado)->usuario === $user->id;
        $vePorPermiso = $user->hasDirectPermission('soporte.lista.agente')
            || $user->hasDirectPermission('soporte.lista.todo')
            || $user->hasDirectPermission('soporte.lista.administrador');

        abort_unless($esCreador || $esAsignado || $vePorPermiso, 404);
    }

    /**
     * Área de quien CREÓ cada soporte, resuelta desde roles.area (Matriz de Permisos) — mismo
     * criterio que areaDelUsuario() en Interacciones: prefiere un rol que SÍ tenga área sobre uno
     * que no, para los perfiles legado con más de uno. En un solo lote (no una consulta por
     * fila) para no volver N+1 al recorrer $soportes en la vista.
     *
     * @return \Illuminate\Support\Collection<int, string|null> área por id de usuario creador
     */
    private function resolverAreasPorCreador($soportes)
    {
        $idsCreadores = $soportes->pluck('id_users')->filter()->unique();

        return DB::table('actions')
            ->join('roles', 'roles.id', '=', 'actions.role_id')
            ->whereIn('actions.user_id', $idsCreadores)
            ->orderByRaw('roles.area is null')
            ->select('actions.user_id', 'roles.area')
            ->get()
            ->unique('user_id')
            ->pluck('area', 'user_id');
    }

    /**
     * Alcance por fila (quién puede VER cada soporte en el listado): quien lo creó, a quien está
     * asignado, o quien tiene el permiso de bandera "agente" (ve cualquiera). Mismo criterio que
     * autorizarVerSoporte() para un ticket puntual, aplicado aquí como filtro de consulta.
     */
    private function aplicarAlcanceListado($query, ?bool $soloPropias = null)
    {
        $user = Auth::user();
        $veTodoPorBandera = !$soloPropias && $user->hasDirectPermission('soporte.lista.agente');

        if ($veTodoPorBandera) {
            return $query;
        }

        $scpUsuarioId = ScpUsuario::where('usuario', $user->id)->value('id');

        return $query->where(function ($q) use ($user, $scpUsuarioId) {
            $q->where('id_users', $user->id);
            if ($scpUsuarioId) {
                $q->orWhere('usuario_escalado', $scpUsuarioId);
            }
        });
    }

    /**
     * Query base de un "tab" del listado — '' o 'todos' es la pestaña TODOS (sin filtrar por
     * estado), cualquier otro valor es el nombre real de un ScpEstado (SinAsignar/EnProceso/...).
     * Antes la pestaña de categoría solo se OCULTABA en el Blade si faltaba el permiso
     * soporte.lista.{categoria} — el endpoint en sí no lo exigía. Ahora que este método también
     * atiende peticiones AJAX directas, el permiso se exige de verdad acá.
     */
    private function baseQueryListado(string $tab, ?bool $soloPropias = null)
    {
        $query = ScpSoporte::query();

        if ($tab !== '' && strtolower($tab) !== 'todos') {
            abort_unless(Auth::user()->hasDirectPermission('soporte.lista.'.strtolower($tab)), 404);

            $idEstado = ScpEstado::idPorNombre($tab);
            $query->where('estado', $idEstado ?? 0);
        }

        return $this->aplicarAlcanceListado($query, $soloPropias);
    }

    /** Filtros del cuadro "Buscar y Filtrar" — antes filtraban en el navegador sobre todo lo cargado. */
    private function aplicarFiltrosListado($query, Request $request)
    {
        if ($request->filled('area')) {
            $area = $request->input('area');
            $query->whereHas('cargo.gdoArea', fn ($q) => $q->where('nombre', $area));
        }
        if ($request->filled('prioridad')) {
            $query->whereHas('prioridad', fn ($q) => $q->where('nombre', $request->input('prioridad')));
        }
        if ($request->filled('usuario')) {
            $query->whereHas('usuario', fn ($q) => $q->where('name', $request->input('usuario')));
        }
        if ($request->filled('asignado')) {
            $query->whereHas('scpUsuarioAsignado.maeTercero', fn ($q) => $q->where('nom_ter', $request->input('asignado')));
        }
        if ($request->filled('fecha')) {
            $query->whereDate('scp_soportes.created_at', $request->input('fecha'));
        }
        if ($request->filled('search.value')) {
            $buscar = '%'.$request->input('search.value').'%';
            $query->where(function ($q) use ($buscar) {
                $q->where('detalles_soporte', 'like', $buscar)
                  ->orWhere('id', 'like', $buscar);
            });
        }

        return $query;
    }

    /**
     * Una fila lista para DataTables — el HTML de cada celda se arma aquí (server-side) en vez de
     * en el navegador, para no duplicar toda la lógica de badges/colores en JS. DataTables no
     * escapa el contenido de las celdas por defecto, así que esto se renderiza tal cual.
     */
    private function formatearFilaSoporteAjax(ScpSoporte $soporte, $areasPorCreador, $estadosParaCambio = []): array
    {
        $areaModal = $soporte->cargo->gdoArea->nombre ?? 'Soporte';
        $prioridadNombre = $soporte->prioridad->nombre ?? '';
        $estadoNombre = $soporte->estadoSoporte->nombre ?? '';

        $prioridadIcono = match ($prioridadNombre) {
            'Alta' => 'feather-alert-triangle',
            'Media' => 'feather-alert-circle',
            'Baja' => 'feather-info',
            default => 'feather-help-circle',
        };
        $prioridadEstilo = match ($prioridadNombre) {
            'Alta' => 'background-color: #FFD6E0 !important; color: #D63384 !important; border: 1px solid #FFB3C1 !important;',
            'Media' => 'background-color: #FFF4E6 !important; color: #FF9800 !important; border: 1px solid #FFE0B2 !important;',
            'Baja' => 'background-color: #E6F3FF !important; color: #5C7CFA !important; border: 1px solid #C5D9FF !important;',
            default => 'background-color: #F3E5F5 !important; color: #9C27B0 !important; border: 1px solid #E1BEE7 !important;',
        };
        // Los nombres reales del catálogo son SinAsignar/EnProceso/Revision/Cerrado (sin
        // espacios ni tildes, ver ScpEstado) — antes se comparaba contra 'Pendiente'/'En Proceso'
        // (con espacio), que nunca coincidían con nada real, así que todo menos "Cerrado" caía
        // siempre al color/ícono por defecto (rosa) sin importar el estado real.
        $estadoIcono = match ($estadoNombre) {
            'SinAsignar' => 'feather-user-x',
            'EnProceso' => 'feather-loader',
            'Revision' => 'feather-eye',
            'Cerrado' => 'feather-check-circle',
            default => 'feather-help-circle',
        };
        $estadoEstilo = match ($estadoNombre) {
            'SinAsignar' => 'background-color: #FFF8E1 !important; color: #F57C00 !important; border: 1px solid #FFECB3 !important;',
            'EnProceso' => 'background-color: #E1F5FE !important; color: #0288D1 !important; border: 1px solid #B3E5FC !important;',
            'Revision' => 'background-color: #F3E5F5 !important; color: #9C27B0 !important; border: 1px solid #E1BEE7 !important;',
            'Cerrado' => 'background-color: #E8F5E8 !important; color: #2E7D32 !important; border: 1px solid #C8E6C9 !important;',
            default => 'background-color: #FCE4EC !important; color: #C2185B !important; border: 1px solid #F8BBD0 !important;',
        };

        $areaCreador = mb_strtoupper($areasPorCreador[$soporte->id_users] ?? '', 'UTF-8') ?: 'N/A';
        $nombreAsignado = $soporte->scpUsuarioAsignado->maeTercero->nom_ter ?? null;

        $idCol = '<a href="javascript:void(0)" class="soporte-id fw-bold text-decoration-none pastel-link"'
            .' data-id="'.$soporte->id.'"'
            .' data-fecha="'.e($soporte->created_at->format('d/m/Y H:i')).'"'
            .' data-creado="'.e($soporte->usuario->name ?? 'N/A').'"'
            .' data-area="'.e($areaModal).'"'
            .' data-categoria="'.e($soporte->categoria->nombre ?? 'N/A').'"'
            .' data-tipo="'.e($soporte->tipo->nombre ?? 'N/A').'"'
            .' data-subtipo="'.e($soporte->subTipo->nombre ?? 'N/A').'"'
            .' data-prioridad="'.e($prioridadNombre).'"'
            .' data-detalles="'.e($soporte->detalles_soporte).'"'
            .' data-updated="'.e($soporte->updated_at->format('d/m/Y H:i')).'"'
            .' data-maetercero="'.e($areaCreador).'"'
            .' data-escalado="'.e($nombreAsignado ?? 'Sin escalar').'"'
            .' data-estado="'.e($estadoNombre).'">#'.$soporte->id.'</a>';

        $fechaCol = '<div class="d-flex flex-column"><span class="small">'.$soporte->created_at->format('d/m/Y')
            .'</span><small class="text-muted" style="font-size: 0.65rem;">'.$soporte->created_at->format('H:i').'</small></div>';

        $creadoCol = '<div class="d-flex align-items-center">'
            .'<div class="avatar-xs-excel pastel-avatar-primary text-white rounded-circle d-flex align-items-center justify-content-center me-1">'
            .e(strtoupper(substr($soporte->usuario->name ?? 'N/A', 0, 1))).'</div>'
            .'<span class="small">'.e($soporte->usuario->name ?? 'N/A').'</span></div>';

        $areaCol = '<span style="background-color: #F8E8FF !important; color: #6B5B95 !important; border-radius: 6px; padding: 1px 4px; font-size: 0.65rem; font-weight: 500; border: 1px solid #E8D8FF; display: inline-block;">'
            .e($areaModal).'</span>';

        $prioridadCol = '<div style="min-width: 50px; text-align: center; border-radius: 8px; font-weight: 500; font-size: 0.6rem; '.$prioridadEstilo.' padding: 2px 6px; display: flex; align-items: center; justify-content: center;">'
            .'<i class="feather '.$prioridadIcono.'" style="font-size: 8px; margin-right: 2px;"></i><span>'.e($prioridadNombre).'</span></div>';

        $descripcionCol = '<div class="description-cell-excel" title="'.e($soporte->detalles_soporte).'" data-bs-toggle="tooltip" data-bs-placement="top">'
            .'<span class="small">'.e(Str::limit($soporte->detalles_soporte, 35)).'</span></div>';

        $asignadoCol = $nombreAsignado
            ? '<div class="d-flex align-items-center"><div class="avatar-xs-excel pastel-avatar-success text-white rounded-circle d-flex align-items-center justify-content-center me-1">'.e(strtoupper(substr($nombreAsignado, 0, 1))).'</div><span class="small">'.e($nombreAsignado).'</span></div>'
            : '<span class="text-muted small">Sin asignar</span>';

        $estadoCol = '<div style="min-width: 60px; text-align: center; border-radius: 8px; font-weight: 500; font-size: 0.6rem; '.$estadoEstilo.' padding: 2px 6px; display: flex; align-items: center; justify-content: center;">'
            .'<i class="feather '.$estadoIcono.'" style="font-size: 8px; margin-right: 2px;"></i><span>'.e($estadoNombre).'</span></div>';

        // Cambio de estado rápido sin entrar al detalle: antes había que abrir el ticket,
        // cambiar el estado ahí, y volver al listado — y si se volvía con el botón "Atrás" del
        // navegador, a veces se veía una copia en caché de la fila desactualizada, dando la
        // sensación de que "se graba pero sigue apareciendo" aunque el guardado sí funcionaba.
        // Con esto se guarda por AJAX y se recarga solo esta tabla, sin navegar a ningún lado.
        // "Revision" queda afuera a propósito: esa transición necesita elegir un revisor (ver
        // cambiarEstado()), que no cabe en un clic rápido de una sola opción.
        $itemsEstado = collect($estadosParaCambio)
            ->filter(fn ($e) => $e->id != $soporte->estado)
            ->map(fn ($e) => '<li><a class="dropdown-item cambiar-estado-rapido" href="javascript:void(0)" data-soporte-id="'.$soporte->id.'" data-estado-id="'.$e->id.'">'.e($e->nombre).'</a></li>')
            ->implode('');

        $accionesCol = '<div class="btn-group btn-group-sm">'
            .'<button type="button" class="btn btn-sm btn-light" style="padding: 2px 6px; font-size: 0.7rem;" onclick="window.location.href=\''.route('soportes.soportes.show', ['scpSoporte' => $soporte->id]).'\'"><i class="feather-eye me-1"></i> Ver</button>'
            .'<button type="button" class="btn btn-sm btn-light dropdown-toggle dropdown-toggle-split" style="padding: 2px 4px;" data-bs-toggle="dropdown" aria-expanded="false" title="Cambiar estado"><span class="visually-hidden">Cambiar estado</span></button>'
            .'<ul class="dropdown-menu dropdown-menu-end">'.$itemsEstado.'</ul>'
            .'</div>';

        return [
            'id_col' => $idCol,
            'fecha_col' => $fechaCol,
            'creado_col' => $creadoCol,
            'area_col' => $areaCol,
            'categoria_col' => e($soporte->categoria->nombre ?? 'N/A'),
            'tipo_col' => e($soporte->tipo->nombre ?? 'N/A'),
            'prioridad_col' => $prioridadCol,
            'descripcion_col' => $descripcionCol,
            'asignado_col' => $asignadoCol,
            'estado_col' => $estadoCol,
            'acciones_col' => $accionesCol,
        ];
    }

    public function index(Request $request)
    {
        // Cierre automático: lo ideal es que corra solo por el scheduler
        // (soportes:cerrar-automatico, cada hora — ver routes/console.php), pero este proyecto
        // todavía no tiene cron/supervisor real llamando schedule:run. Mientras tanto, este
        // disparador de respaldo hace lo mismo pero limitado a una vez por hora entre TODAS las
        // visitas (antes corría en cada una, con subconsultas correlacionadas de por medio).
        Cache::remember('soportes_cierre_automatico_ejecutado', 3600, function () {
            app(\App\Services\Soportes\CierreAutomaticoService::class)->ejecutar();

            return true;
        });

        // ==========================================
        // RESPUESTA AJAX PARA DATATABLES (SERVER-SIDE)
        // ==========================================
        // Antes esta pantalla cargaba TODOS los soportes de una sola vez (con ->get()) y
        // DataTables paginaba/filtraba en el navegador sobre ese HTML completo — con 202
        // registros ya eran 222 consultas y toda esa HTML viajando al celular en cada visita.
        // Ahora cada pestaña pide su propia página al servidor.
        if ($request->ajax()) {
            $tab = (string) $request->input('tab', '');
            $query = $this->baseQueryListado($tab);
            $this->aplicarFiltrosListado($query, $request);

            $totalSinFiltrar = (clone $this->baseQueryListado($tab))->count();
            $totalFiltrado = (clone $query)->count();

            $start = (int) $request->input('start', 0);
            $length = (int) $request->input('length', 20);

            $soportesQuery = $query
                ->with(['tipo', 'subTipo', 'prioridad', 'usuario', 'cargo.gdoArea', 'lineaCredito', 'scpUsuarioAsignado.maeTercero', 'estadoSoporte', 'categoria'])
                ->orderBy('created_at', 'desc');

            // length=-1 es la convención de DataTables para "todos" — la usa el botón de
            // exportar cuando pide TODO lo filtrado, no solo la página visible (ver
            // exportarTodoFiltrado() en la vista). Tope defensivo de 5000 para no abrir la
            // puerta a un export descontrolado si el volumen crece mucho.
            if ($length === -1) {
                $soportesPagina = $soportesQuery->take(5000)->get();
            } else {
                $soportesPagina = $soportesQuery->skip($start)->take($length)->get();
            }

            $areasPorCreador = $this->resolverAreasPorCreador($soportesPagina);
            $estadosParaCambio = ScpEstado::where('nombre', '!=', 'Revision')->orderBy('nombre')->get(['id', 'nombre']);
            $data = $soportesPagina->map(fn ($s) => $this->formatearFilaSoporteAjax($s, $areasPorCreador, $estadosParaCambio))->values();

            return response()->json([
                'draw' => (int) $request->input('draw'),
                'recordsTotal' => $totalSinFiltrar,
                'recordsFiltered' => $totalFiltrado,
                'data' => $data,
            ]);
        }

        $categoriaActivaPorDefecto = 'SinAsignar'; // Puedes cambiar esto a 'Pendiente', 'Cerrado', etc.

        // Carga normal (no AJAX): ya no se traen todas las filas, solo lo necesario para armar
        // las pestañas (conteos y qué categorías existen) y las opciones de los filtros — órdenes
        // de magnitud más liviano que antes.
        $todasLasCategorias = ScpEstado::orderBy('nombre')->pluck('nombre');
        $categorias = $todasLasCategorias->mapWithKeys(function ($nombre) {
            $puedeVerCategoria = Auth::user()->hasDirectPermission('soporte.lista.'.strtolower($nombre));
            $total = $puedeVerCategoria
                ? (clone $this->aplicarAlcanceListado(ScpSoporte::where('estado', ScpEstado::idPorNombre($nombre) ?? 0)))->count()
                : 0;

            return [$nombre => $total];
        })->filter(function ($total, $nombre) {
            return Auth::user()->hasDirectPermission('soporte.lista.'.strtolower($nombre));
        });

        $totalTodos = (clone $this->aplicarAlcanceListado(ScpSoporte::query()))->count();

        // Opciones de los filtros (Área/Prioridad/Usuario/Asignado) — antes salían de
        // $soportes->pluck(...)->unique() sobre TODO lo cargado; ahora son consultas propias,
        // livianas, con DISTINCT.
        $opcionesArea = DB::table('scp_soportes')
            ->join('gdo_cargo', 'scp_soportes.id_gdo_cargo', '=', 'gdo_cargo.id')
            ->join('gdo_area', 'gdo_cargo.GDO_area_id', '=', 'gdo_area.id')
            ->distinct()->orderBy('gdo_area.nombre')->pluck('gdo_area.nombre');
        $opcionesPrioridad = ScpPrioridad::orderBy('nombre')->pluck('nombre');
        $opcionesUsuario = DB::table('scp_soportes')
            ->join('users', 'scp_soportes.id_users', '=', 'users.id')
            ->distinct()->orderBy('users.name')->pluck('users.name');
        $opcionesAsignado = DB::table('scp_soportes')
            ->join('scp_usuarios', 'scp_soportes.usuario_escalado', '=', 'scp_usuarios.id')
            ->join('MaeTerceros', 'MaeTerceros.cod_ter', '=', 'scp_usuarios.cod_ter')
            ->distinct()->orderBy('MaeTerceros.nom_ter')->pluck('MaeTerceros.nom_ter');

        return view('soportes.soportes.index', compact(
            'categorias',
            'categoriaActivaPorDefecto',
            'totalTodos',
            'opcionesArea',
            'opcionesPrioridad',
            'opcionesUsuario',
            'opcionesAsignado',
        ));
    }

    public function create()
    {
        // $tipos, $terceros, $usuarios y $cargos se cargaban aquí pero NUNCA se usan en
        // create.blade.php/form.blade.php — el select de "Tipo" se llena por AJAX
        // (getTiposByCategoria) y el cargo/usuario vienen de $usuario->cargo, no de estas
        // listas. $terceros en particular traía las 26,209 filas de mae_terceros de TODA la ERP
        // sin ningún filtro, cada vez que alguien abría "Nuevo Soporte" — esa era la causa real
        // de que la pantalla se quedara cargando sin abrir nunca.
        $categorias = ScpCategoria::all();
        $prioridades = ScpPrioridad::all();
        $lineas = LineaCredito::select('id', 'nombre')->get();

        $usuario = User::find(Auth::id());

        // 🔹 Calcular el próximo ID de soporte
        $ultimo = ScpSoporte::max('id');
        $proximoId = $ultimo ? $ultimo + 1 : 1;

        // 🔹 Enviar $proximoId a la vista
        return view(
            'soportes.soportes.create',
            compact(
                'categorias',
                'prioridades',
                'lineas',
                'usuario',
                'proximoId', // 👈 este es nuevo
            ),
        );
    }

    // ==========================
    // VER SOPORTE (form)
    // ==========================
    public function store(Request $request)
    {
        $request->validate([
            'detalles_soporte' => 'required|string',
            'id_gdo_cargo' => 'nullable|integer|exists:gdo_cargo,id',
            'id_cre_lineas_creditos' => 'nullable|integer|exists:cre_lineas_creditos,id',
            'id_categoria' => 'required|exists:scp_categorias,id',
            'id_scp_tipo' => 'required|exists:scp_tipos,id',
            'id_scp_prioridad' => 'required|exists:scp_prioridads,id',
            'id_users' => 'required|exists:users,id',
            'id_scp_sub_tipo' => 'required|exists:scp_sub_tipos,id',
            'soporte' => 'nullable|file|mimes:pdf,jpeg,jpg,png|max:10240',
            'usuario_escalado' => 'nullable|string|max:100',
        ]);

        $data = [
            'detalles_soporte' => $request->detalles_soporte,
            'timestam' => now(),
            'id_gdo_cargo' => $request->id_gdo_cargo,
            'id_cre_lineas_creditos' => $request->id_cre_lineas_creditos,
            'id_categoria' => $request->id_categoria,
            'id_scp_tipo' => $request->id_scp_tipo,
            'id_scp_prioridad' => $request->id_scp_prioridad,
            'id_users' => $request->id_users,
            'id_scp_sub_tipo' => $request->id_scp_sub_tipo,
            'estado' => 1,
            'usuario_escalado' => $request->usuario_escalado,
        ];

        // ✅ Subir archivo directamente a S3 con ruta estructurada
        if ($request->hasFile('soporte')) {
            $file = $request->file('soporte');

            // Limpiar nombre del archivo
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();
            $cleanName = Str::slug($originalName, '-'); // elimina espacios y caracteres raros

            // Estructura de carpeta (ejemplo: corpentunida/soportes/usuario_5/2025/10/)
            $userId = Auth::id();
            $year = now()->year;
            $month = now()->format('m');
            $filename = time() . '_' . $cleanName . '.' . $extension;
            $folderPath = "corpentunida/soportes/usuario_{$userId}/{$year}/{$month}";

            // Guardar en S3 (privado)
            $path = Storage::disk('s3')->putFileAs($folderPath, $file, $filename);

            // Guardamos solo el path en la BD
            $data['soporte'] = $path;
        }

        ScpSoporte::create($data);

        return redirect()->route('soportes.soportes.index')->with('success', 'Soporte creado exitosamente.');
    }

    // ==========================
    // 👀 VER SOPORTE (solo abrir)
    // ==========================
    public function verSoporte($id)
    {
        $soporte = ScpSoporte::findOrFail($id);
        $this->autorizarVerSoporte($soporte);

        if (!$soporte->soporte || !Storage::disk('s3')->exists($soporte->soporte)) {
            return back()->with('error', 'Archivo no disponible o no existe en S3.');
        }

        try {
            /** @var \Illuminate\Filesystem\AwsS3V3Adapter|\Illuminate\Filesystem\FilesystemAdapter $disk */
            $disk = Storage::disk('s3');

            $urlTemporal = $disk->temporaryUrl($soporte->soporte, now()->addMinutes(5));

            return redirect($urlTemporal);
        } catch (\Exception $e) {
            // 🔴 Capturar error de permisos o región y mostrar mensaje
            return back()->with('error', 'No se pudo acceder al archivo en S3: ' . $e->getMessage());
        }
    }

    // ============================
    // ⬇️ DESCARGAR SOPORTE PRIVADO
    // ============================
    public function descargarSoporte($id)
    {
        $soporte = ScpSoporte::findOrFail($id);
        $this->autorizarVerSoporte($soporte);

        if (!$soporte->soporte) {
            abort(404, 'Archivo no encontrado.');
        }

        /** @var \Illuminate\Filesystem\AwsS3V3Adapter|\Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk('s3');

        if (!$disk->exists($soporte->soporte)) {
            return back()->with('error', 'Archivo no disponible.');
        }

        // ✅ Genera URL temporal válida 2 minutos para descargar
        $urlTemporal = $disk->temporaryUrl($soporte->soporte, now()->addMinutes(2));

        return redirect($urlTemporal);
    }

    public function show(ScpSoporte $scpSoporte)
    {
        $this->autorizarVerSoporte($scpSoporte);

        $scpSoporte->load([
            'tipo',
            'subTipo',
            'prioridad',
            'usuario',
            'cargo',
            'lineaCredito',
            'observaciones' => function ($query) {
                $query->with(['estado', 'usuario', 'tipoObservacion', 'scpUsuarioAsignado.maeTercero'])->orderBy('timestam', 'desc'); // 👈 asegúrate que el campo exista
            },
        ]);

        $estados = ScpEstado::all();
        $tiposObservacion = ScpTipoObservacion::all();
        $usuariosEscalamiento = ScpUsuario::with('maeTercero')->get();

        return view('soportes.soportes.show', [
            'soporte' => $scpSoporte,
            'estados' => $estados,
            'tiposObservacion' => $tiposObservacion,
            'usuariosEscalamiento' => $usuariosEscalamiento,
        ]);
    }

    public function edit(ScpSoporte $scpSoporte)
    {
        $this->autorizarVerSoporte($scpSoporte);

        // Mismo caso que create(): $tipos, $terceros, $usuarios y $cargos se cargaban aquí pero
        // nunca se usan en edit.blade.php/form.blade.php — $terceros en particular traía las
        // 26,209 filas de mae_terceros de toda la ERP en cada carga de esta pantalla.
        $categorias = ScpCategoria::all();
        $prioridades = ScpPrioridad::all();
        $lineas = LineaCredito::select('id', 'nombre')->get();

        $scpSoporte->load([
            'observaciones' => function ($query) {
                $query->with(['estado', 'usuario', 'tipoObservacion'])->orderBy('timestam', 'desc');
            },
        ]);

        $estados = ScpEstado::all();
        $tiposObservacion = ScpTipoObservacion::all();

        $usuario = User::find(Auth::id());

        return view('soportes.soportes.edit', compact('scpSoporte', 'categorias', 'prioridades', 'lineas', 'estados', 'tiposObservacion', 'usuario'));
    }

    public function update(Request $request, $id)
    {
        // Validar únicamente la prioridad
        $request->validate([
            'id_scp_prioridad' => 'required|exists:scp_prioridads,id',
        ]);

        // Buscar el soporte
        $soporte = ScpSoporte::findOrFail($id);
        $this->autorizarVerSoporte($soporte);

        // Actualizar la prioridad
        $soporte->id_scp_prioridad = $request->id_scp_prioridad;
        $soporte->save();

        // Redirigir con mensaje
        return redirect()->route('soportes.soportes.index')->with('success', 'Prioridad del soporte actualizada correctamente.');
    }

    /* REVISAR - dd($request->all()); */
    public function storeObservacion(Request $request, ScpSoporte $scpSoporte)
    {
        $this->autorizarVerSoporte($scpSoporte);

        // Mover un ticket a "Revision" es, en la práctica, pasarle la responsabilidad a otra
        // persona — igual que "Escalamiento" — pero antes no exigía elegir a quién, así que
        // usuario_escalado se quedaba con quien lo tenía antes (que ya no tiene ninguna
        // injerencia) y la alerta de "soportes sin cerrar" seguía molestando a esa persona en vez
        // de a quien debía revisar. Espejo del toggle de show.blade.php (evaluarCampoAsignacion).
        $exigeAsignar = fn () => (int) $request->input('id_scp_estados') === ScpEstado::idPorNombre('Revision')
            || (int) $request->input('id_tipo_observacion') === ScpTipoObservacion::idPorNombre('Escalamiento');

        $request->validate([
            'observacion' => 'required|string',
            'id_scp_estados' => 'required|exists:scp_estados,id',
            'id_tipo_observacion' => 'required|exists:scp_tipo_observacions,id',
            'id_scp_usuario_asignado' => [Rule::requiredIf($exigeAsignar), 'nullable', 'integer', 'exists:scp_usuarios,id'],
            'calcification' => ['nullable', 'integer', 'min:1', 'max:5'], // Validación para la calificación
            'adjunto' => 'nullable|file|mimes:pdf,jpeg,jpg,png|max:10240',
        ], [
            'id_scp_usuario_asignado.required' => 'Debes elegir a quién queda asignado el ticket (Revisión/Escalamiento no pueden quedar sin responsable).',
        ]);

        $observacionData = [
            'observacion' => $request->observacion,
            'timestam' => now(),
            'id_scp_soporte' => $scpSoporte->id,
            'id_scp_estados' => $request->id_scp_estados,
            'id_users' => Auth::id(),
            'id_users_asignado' => $request->id_scp_usuario_asignado ?? null,
            'id_tipo_observacion' => $request->id_tipo_observacion,
        ];

        if ($request->has('calcification')) {
            $observacionData['calcification'] = $request->calcification;
        }

        // Adjunto propio de esta observación — mismo esquema de carpetas que el archivo original
        // del ticket (store()), pero separado por observación para no pisar el del ticket.
        if ($request->hasFile('adjunto')) {
            $file = $request->file('adjunto');
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();
            $cleanName = Str::slug($originalName, '-');
            $filename = time() . '_' . $cleanName . '.' . $extension;
            $folderPath = "corpentunida/soportes/usuario_" . Auth::id() . "/observaciones/" . $scpSoporte->id;
            $observacionData['archivo'] = Storage::disk('s3')->putFileAs($folderPath, $file, $filename);
        }

        $observacionCreada = $scpSoporte->observaciones()->create($observacionData);

        $updateData = [
            'estado' => $request->id_scp_estados,
        ];

        if ($request->filled('id_scp_usuario_asignado') && $request->input('id_scp_usuario_asignado') != '0') {
            $updateData['usuario_escalado'] = $request->input('id_scp_usuario_asignado');
        }

        $scpSoporte->update($updateData);

        // Antes comparaba contra un ID fijo (== 3) y LUEGO volvía a buscar el registro para
        // confirmar por nombre — dos pasos para lo mismo, y el primero se rompía en silencio si
        // el catálogo cambiaba de orden. idPorNombre() ya cachea la búsqueda, así que buscar
        // directo por nombre no cuesta una consulta extra en el camino normal.
        $esEscalamiento = (int) $request->id_tipo_observacion === ScpTipoObservacion::idPorNombre('Escalamiento');

        // ================================
        //   LÓGICA DE ENVÍO DE CORREOS
        // ================================
        // Envuelto en try/catch: si el correo falla (SMTP caído, credenciales, lo que sea), la
        // observación y el estado YA quedaron guardados arriba — antes, una excepción acá
        // tumbaba toda la petición con un 500 después de haber guardado los cambios, dejando al
        // usuario sin saber si su observación sí se registró o no.
        if ($esEscalamiento) {
            try {
                if ($request->filled('id_scp_usuario_asignado') && $request->id_scp_usuario_asignado != 0) {
                    $usuarioEscalado = ScpUsuario::with('UserApp')->find($request->id_scp_usuario_asignado);
                    if ($usuarioEscalado && $usuarioEscalado->UserApp && !empty($usuarioEscalado->UserApp->email)) {
                        Mail::to($usuarioEscalado->UserApp->email)->send(new SoporteEscaladoMail($scpSoporte, 'escalado', $observacionCreada));
                    }
                }
                if ($scpSoporte->usuario && !empty($scpSoporte->usuario->email)) {
                    Mail::to($scpSoporte->usuario->email)->send(new SoporteEscaladoMail($scpSoporte, 'creador', $observacionCreada));
                }
            } catch (\Throwable $e) {
                Log::error('Error enviando correo de escalamiento (soporte '.$scpSoporte->id.'): '.$e->getMessage());
            }
        }
        // ================================

        return redirect()->route('soportes.soportes.show', $scpSoporte)->with('success', 'Observación añadida y soporte actualizado exitosamente.');
    }

    public function destroyObservacion(ScpSoporte $scpSoporte, ScpObservacion $scpObservacion)
    {
        $this->autorizarVerSoporte($scpSoporte);

        if ($scpObservacion->id_scp_soporte !== $scpSoporte->id) {
            return redirect()->back()->with('error', 'La observación no pertenece a este soporte.');
        }

        $scpObservacion->delete();

        // El estado del ticket lo fija SIEMPRE la última observación registrada (ver
        // storeObservacion()) — si se borra justo esa, el ticket se queda con un estado
        // "huérfano" que ya no corresponde a ninguna observación real. Se recalcula según lo que
        // quede: la más reciente restante, o "SinAsignar" (id 1 — el mismo con el que nace todo
        // ticket en store()) si no queda ninguna.
        $ultimaRestante = $scpSoporte->observaciones()->orderByDesc('timestam')->first();
        $scpSoporte->update(['estado' => $ultimaRestante->id_scp_estados ?? 1]);

        return redirect()->route('soportes.soportes.show', $scpSoporte)->with('success', 'Observación eliminada exitosamente.');
    }

    // ============================
    // ⬇️ DESCARGAR ADJUNTO DE UNA OBSERVACIÓN PUNTUAL
    // ============================
    public function descargarAdjuntoObservacion($id)
    {
        $observacion = ScpObservacion::with('soporte')->findOrFail($id);
        $this->autorizarVerSoporte($observacion->soporte);

        if (!$observacion->archivo) {
            abort(404, 'Esta observación no tiene un archivo adjunto.');
        }

        /** @var \Illuminate\Filesystem\AwsS3V3Adapter|\Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk('s3');

        if (!$disk->exists($observacion->archivo)) {
            return back()->with('error', 'Archivo no disponible.');
        }

        $urlTemporal = $disk->temporaryUrl($observacion->archivo, now()->addMinutes(2));

        return redirect($urlTemporal);
    }

    // ============================
    // 🔍 BUSCADOR RÁPIDO (Select2 AJAX)
    // ============================
    public function quickSearch(Request $request)
    {
        $termino = trim((string) $request->get('q', ''));
        $user = Auth::user();

        // Mismo criterio de alcance que autorizarVerSoporte(): quien ve cualquier ticket puede
        // buscar en todos, el resto solo encuentra los suyos (creados o asignados).
        $veTodo = $user->hasDirectPermission('soporte.lista.agente')
            || $user->hasDirectPermission('soporte.lista.todo')
            || $user->hasDirectPermission('soporte.lista.administrador');

        $query = ScpSoporte::query();

        if (!$veTodo) {
            $scpUsuarioId = ScpUsuario::where('usuario', $user->id)->value('id');
            $query->where(function ($q) use ($user, $scpUsuarioId) {
                $q->where('id_users', $user->id);
                if ($scpUsuarioId) {
                    $q->orWhere('usuario_escalado', $scpUsuarioId);
                }
            });
        }

        if ($termino !== '') {
            $query->where(function ($q) use ($termino) {
                $q->where('detalles_soporte', 'like', "%{$termino}%");
                if (is_numeric($termino)) {
                    $q->orWhere('id', (int) $termino);
                }
            });
        }

        $resultados = $query->orderByDesc('created_at')->limit(15)->get(['id', 'detalles_soporte']);

        return response()->json([
            'results' => $resultados->map(fn ($s) => [
                'id' => $s->id,
                'text' => '#' . $s->id . ' — ' . Str::limit($s->detalles_soporte, 60),
            ]),
            'pagination' => ['more' => false],
        ]);
    }

    // ============================
    // 👤 ASIGNAR AGENTE (acción rápida, sin pasar por una observación completa)
    // ============================
    public function asignarAgente(Request $request, ScpSoporte $scpSoporte)
    {
        // Reasignar es una acción de triage del equipo, no algo que el simple creador del ticket
        // deba poder hacer — mismo criterio que ya usa la vista para mostrar el selector de
        // "Asignar a Usuario (Escalamiento)".
        $user = Auth::user();
        abort_unless(
            $user->hasDirectPermission('soporte.lista.agente') || $user->hasDirectPermission('soporte.lista.administrador'),
            404,
        );

        $request->validate([
            'id_scp_usuario_asignado' => 'required|exists:scp_usuarios,id',
        ]);

        $scpSoporte->update(['usuario_escalado' => $request->id_scp_usuario_asignado]);

        // Deja rastro en el historial del ticket, igual que hace storeObservacion() con las demás
        // acciones — sin esto, un cambio de agente por esta vía quedaría invisible en la línea de
        // tiempo.
        $agente = ScpUsuario::with(['maeTercero', 'UserApp'])->find($request->id_scp_usuario_asignado);
        $nombreAgente = optional($agente?->maeTercero)->nom_ter
            ?? optional($agente?->UserApp)->name
            ?? 'Agente #' . $request->id_scp_usuario_asignado;

        $scpSoporte->observaciones()->create([
            'observacion' => 'Ticket reasignado a ' . $nombreAgente . '.',
            'timestam' => now(),
            'id_scp_estados' => $scpSoporte->estado,
            'id_users' => Auth::id(),
            'id_users_asignado' => $request->id_scp_usuario_asignado,
            'id_tipo_observacion' => 2, // Accion
        ]);

        return redirect()->back()->with('success', 'Agente asignado correctamente.');
    }

    // ============================
    // 🔄 CAMBIAR ESTADO (acción rápida, sin pasar por una observación completa)
    // ============================
    public function cambiarEstado(Request $request, ScpSoporte $scpSoporte)
    {
        // Mismo criterio que asignarAgente(): cambiar el estado sin dejar una observación de por
        // medio es una acción de equipo, no algo que el creador del ticket deba poder disparar.
        $user = Auth::user();
        abort_unless(
            $user->hasDirectPermission('soporte.lista.agente') || $user->hasDirectPermission('soporte.lista.administrador'),
            404,
        );

        $request->validate([
            'estado' => 'required|exists:scp_estados,id',
        ]);

        // Mover a "Revision" es, en la práctica, pasarle la responsabilidad a otra persona —
        // necesita elegir un revisor (ver storeObservacion() y usuario_escalado), campo que este
        // endpoint no recibe. El cambio rápido desde el listado ya excluye esta opción del menú,
        // pero se valida también aquí por si alguien la dispara directo sin pasar por la UI.
        if ((int) $request->estado === ScpEstado::idPorNombre('Revision')) {
            $mensaje = 'Para mover a "Revisión" hay que elegir quién revisa — entra al detalle del ticket.';
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['ok' => false, 'message' => $mensaje], 422);
            }

            return redirect()->back()->with('error', $mensaje);
        }

        $estadoAnterior = $scpSoporte->estadoSoporte->nombre ?? 'Sin Estado';
        $scpSoporte->update(['estado' => $request->estado]);
        $scpSoporte->refresh();

        $scpSoporte->observaciones()->create([
            'observacion' => 'Estado cambiado de "' . $estadoAnterior . '" a "' . ($scpSoporte->estadoSoporte->nombre ?? '') . '".',
            'timestam' => now(),
            'id_scp_estados' => $request->estado,
            'id_users' => Auth::id(),
            'id_tipo_observacion' => 2, // Accion
        ]);

        // El cambio rápido desde el listado (formatearFilaSoporteAjax) llama esto por AJAX y
        // recarga solo la tabla en vez de navegar — necesita JSON, no un redirect.
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['ok' => true, 'estado' => $scpSoporte->estadoSoporte->nombre ?? '']);
        }

        return redirect()->back()->with('success', 'Estado actualizado correctamente.');
    }

    // ============================
    // 📂 MIS SOPORTES (creados por mí o asignados a mí)
    // ============================
    public function misSoportes(Request $request)
    {
        // Mismo patrón server-side que index() (ver ahí los comentarios), con soloPropias=true:
        // creados por mí o asignados a mí, sin importar la bandera "ver cualquiera".
        if ($request->ajax()) {
            $tab = (string) $request->input('tab', '');
            $query = $this->baseQueryListado($tab, soloPropias: true);
            $this->aplicarFiltrosListado($query, $request);

            $totalSinFiltrar = (clone $this->baseQueryListado($tab, soloPropias: true))->count();
            $totalFiltrado = (clone $query)->count();

            $start = (int) $request->input('start', 0);
            $length = (int) $request->input('length', 20);

            $soportesQuery = $query
                ->with(['tipo', 'subTipo', 'prioridad', 'usuario', 'cargo.gdoArea', 'lineaCredito', 'scpUsuarioAsignado.maeTercero', 'estadoSoporte', 'categoria'])
                ->orderBy('created_at', 'desc');

            // length=-1: ver mismo comentario en index().
            if ($length === -1) {
                $soportesPagina = $soportesQuery->take(5000)->get();
            } else {
                $soportesPagina = $soportesQuery->skip($start)->take($length)->get();
            }

            $areasPorCreador = $this->resolverAreasPorCreador($soportesPagina);
            $estadosParaCambio = ScpEstado::where('nombre', '!=', 'Revision')->orderBy('nombre')->get(['id', 'nombre']);
            $data = $soportesPagina->map(fn ($s) => $this->formatearFilaSoporteAjax($s, $areasPorCreador, $estadosParaCambio))->values();

            return response()->json([
                'draw' => (int) $request->input('draw'),
                'recordsTotal' => $totalSinFiltrar,
                'recordsFiltered' => $totalFiltrado,
                'data' => $data,
            ]);
        }

        $categoriaActivaPorDefecto = 'SinAsignar';

        $todasLasCategorias = ScpEstado::orderBy('nombre')->pluck('nombre');
        $categorias = $todasLasCategorias->mapWithKeys(function ($nombre) {
            $puedeVerCategoria = Auth::user()->hasDirectPermission('soporte.lista.'.strtolower($nombre));
            $total = $puedeVerCategoria
                ? (clone $this->aplicarAlcanceListado(ScpSoporte::where('estado', ScpEstado::idPorNombre($nombre) ?? 0), soloPropias: true))->count()
                : 0;

            return [$nombre => $total];
        })->filter(function ($total, $nombre) {
            return Auth::user()->hasDirectPermission('soporte.lista.'.strtolower($nombre));
        });

        $totalTodos = (clone $this->aplicarAlcanceListado(ScpSoporte::query(), soloPropias: true))->count();

        $opcionesArea = DB::table('scp_soportes')
            ->join('gdo_cargo', 'scp_soportes.id_gdo_cargo', '=', 'gdo_cargo.id')
            ->join('gdo_area', 'gdo_cargo.GDO_area_id', '=', 'gdo_area.id')
            ->distinct()->orderBy('gdo_area.nombre')->pluck('gdo_area.nombre');
        $opcionesPrioridad = ScpPrioridad::orderBy('nombre')->pluck('nombre');
        $opcionesUsuario = DB::table('scp_soportes')
            ->join('users', 'scp_soportes.id_users', '=', 'users.id')
            ->distinct()->orderBy('users.name')->pluck('users.name');
        $opcionesAsignado = DB::table('scp_soportes')
            ->join('scp_usuarios', 'scp_soportes.usuario_escalado', '=', 'scp_usuarios.id')
            ->join('MaeTerceros', 'MaeTerceros.cod_ter', '=', 'scp_usuarios.cod_ter')
            ->distinct()->orderBy('MaeTerceros.nom_ter')->pluck('MaeTerceros.nom_ter');

        // Reutiliza la misma vista que index() (el tablero por categorías ya es genérico) —
        // 'esVistaPersonal' le permite a la vista distinguir el título.
        return view('soportes.soportes.index', compact(
            'categorias',
            'categoriaActivaPorDefecto',
            'totalTodos',
            'opcionesArea',
            'opcionesPrioridad',
            'opcionesUsuario',
            'opcionesAsignado',
        ))->with('esVistaPersonal', true);
    }

    public function getSubTipos($tipoId)
    {
        $subTipos = ScpSubTipo::where('scp_tipo_id', $tipoId)
            ->orderBy('nombre')
            ->get(['id', 'nombre']);

        return response()->json($subTipos);
    }

    public function getTiposByCategoria($categoriaId)
    {
        try {
            $tipos = ScpTipo::where('id_categoria', $categoriaId)
                ->orderBy('nombre')
                ->get(['id', 'nombre']);

            return response()->json($tipos);
        } catch (\Exception $e) {
            Log::error('Error en getTiposByCategoria: ' . $e->getMessage());
            return response()->json(['error' => 'Error interno del servidor'], 500);
        }
    }

    // ==========================
    // NOTIFICACIONES
    // ==========================
    public function getNotificaciones()
    {
        $userId = Auth::id();
        $usuarioEscalado = ScpUsuario::where('usuario', $userId)->first();

        // Solo contar soportes que no están cerrados para la campana
        $total = ScpSoporte::query()
            ->where('estado', '!=', '4') // Excluir cerrados (ID=4)
            ->when(
                $usuarioEscalado,
                function ($q) use ($usuarioEscalado, $userId) {
                    $q->where('id_users', $userId)->orWhere('usuario_escalado', $usuarioEscalado->id);
                },
                function ($q) use ($userId) {
                    $q->where('id_users', $userId);
                },
            )
            ->count();

        return response()->json(['total' => $total]);
    }

    public function getNotificacionesDetalladas()
    {
        if (!request()->ajax()) {
            abort(403);
        }

        $userId = Auth::id();

        $usuarioEscaladoId = ScpUsuario::where('usuario', $userId)->value('id');

        $query = ScpSoporte::query()->where(function ($q) use ($userId, $usuarioEscaladoId) {
            $q->where('id_users', $userId);

            if ($usuarioEscaladoId) {
                $q->orWhere('usuario_escalado', $usuarioEscaladoId);
            }
        });

        // SOLO contadores (muy rápido)
        $counts = ScpSoporte::where(function ($q) use ($userId, $usuarioEscaladoId) {
            $q->where('id_users', $userId);

            if ($usuarioEscaladoId) {
                $q->orWhere('usuario_escalado', $usuarioEscaladoId);
            }
        })
            ->selectRaw('estado, COUNT(*) as total')
            ->groupBy('estado')
            ->pluck('total', 'estado');

        // SOLO últimos soportes
        $soportes = $query
            ->with(['estadoSoporte:id,nombre', 'prioridad:id,nombre', 'usuario:id,name'])
            ->orderByDesc('updated_at')
            ->limit(20)
            ->get(['id', 'id_users', 'detalles_soporte', 'estado', 'id_scp_prioridad', 'updated_at'])
            ->groupBy('estado');

        return response()->json([
            'sinAsignar_count' => $counts[1] ?? 0,
            'enProceso_count' => $counts[2] ?? 0,
            'revision_count' => $counts[3] ?? 0,
            'cerrados_count' => $counts[4] ?? 0,
            'total' => ($counts[1] ?? 0) + ($counts[2] ?? 0) + ($counts[3] ?? 0),

            'sinAsignar' => isset($soportes[1]) ? $soportes[1]->map(fn($s) => $this->formatearSoporte($s)) : [],
            'enProceso' => isset($soportes[2]) ? $soportes[2]->map(fn($s) => $this->formatearSoporte($s)) : [],
            'revision' => isset($soportes[3]) ? $soportes[3]->map(fn($s) => $this->formatearSoporte($s)) : [],
            'cerrados' => isset($soportes[4]) ? $soportes[4]->map(fn($s) => $this->formatearSoporte($s)) : [],
        ]);
    }

    // Método auxiliar para formatear soportes
    private function formatearSoporte($soporte)
    {
        $prioridad = $soporte->prioridad->nombre ?? 'Baja';
        $color = match ($prioridad) {
            'Alta' => 'danger',
            'Media' => 'warning',
            'Baja' => 'primary',
            default => 'gray',
        };

        // Determinar el nombre del estado según el ID
        $estadoNombre = match ($soporte->estado) {
            '1' => 'Sin Asignar',
            '2' => 'En Proceso',
            '3' => 'En Revisión',
            '4' => 'Cerrado',
            default => $soporte->estadoSoporte->nombre ?? 'Sin Estado',
        };

        return [
            'id' => $soporte->id,
            'usuario_nombre' => $soporte->usuario->nombre_corto ?? '',
            'detalles_soporte' => Str::limit($soporte->detalles_soporte, 80),
            'prioridad' => $prioridad,
            'estado' => $estadoNombre,
            'estado_id' => $soporte->estado,
            'prioridad_color' => $color,
            'fecha_creacion' => Carbon::parse($soporte->updated_at)->diffForHumans(),
            'cerrado' => $soporte->estado == '4',
        ];
    }

    // ==========================
    // POR REVISAR
    // ==========================

    public function pendientes()
    {
        $soportesPendientes = ScpSoporte::with(['tipo', 'subTipo', 'prioridad', 'usuario', 'cargo.gdoArea', 'lineaCredito', 'scpUsuarioAsignado.maeTercero', 'estadoSoporte', 'categoria'])
            ->whereHas('estadoSoporte', function ($q) {
                $q->where('nombre', 'Pendiente');
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return view('soportes.soportes.categorias.pendientes', compact('soportesPendientes'));
    }
    public function sinAsignar()
    {
        $soportesSinAsignar = ScpSoporte::with(['tipo', 'subTipo', 'prioridad', 'usuario', 'cargo.gdoArea', 'lineaCredito', 'scpUsuarioAsignado.maeTercero', 'estadoSoporte', 'categoria'])
            ->whereHas('categoria', function ($q) {
                $q->where('nombre', 'SinAsignar');
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return view('soportes.soportes.categorias.sinAsignar', compact('soportesSinAsignar'));
    }
}
