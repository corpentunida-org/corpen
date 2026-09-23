<?php

namespace App\Http\Controllers\Interacciones;

use App\Http\Controllers\Controller;
use App\Models\Archivo\GdoArea;
use App\Models\Archivo\GdoCargo;
use App\Models\Cartera\CarComprobantePago;
use App\Models\Contabilidad\ConCuentaBancaria;
use App\Models\Creditos\LineaCredito;
use App\Models\Interacciones\IntChannel;
use App\Models\Interacciones\Interaction;
use App\Models\Interacciones\IntNextAction;
use App\Models\Interacciones\IntOutcome;
//use App\Models\Maestras\MaeCongregacion; Sin uso actual.
use App\Models\Interacciones\IntSeguimiento;
use App\Models\Interacciones\IntType;
use App\Models\Maestras\MaeDistritos;
use App\Models\Maestras\MaeTerceros;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
// Importar los modelos para los catálogos
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class InteractionController extends Controller
{
    /**
     * Alcance por agente para los Informes — mismo criterio de 3 niveles que
     * alcanceInteracciones() (Listado/Auditoría), pero devuelto como una lista de IDs de agente
     * en vez de una query ya armada: los informes reconstruyen la query varias veces (KPIs,
     * gráficos, seguimientos...) así que es más simple intersectar $agentIds en cada una.
     *  - interacciones.informes.todosagentes: sin restricción, cualquier agente ($agentIds null).
     *  - interacciones.informes.area (o .todosagentes): solo agentes de SU MISMA área. Con
     *    .todosagentes se puede elegir explícitamente otra vía ?modo=area&area_id=.
     *  - Por defecto (o pidiendo un modo sin el permiso): solo el propio agente — igual que
     *    antes de este cambio, nunca "todos" ni "el área" sin haberlo pedido a propósito.
     * Además soporta el filtro puntual `agent_id` que ya existía (drill-down a UN agente,
     * intersectado con lo que el modo ya delimitó — pedir uno fuera de alcance simplemente no
     * encuentra nada, no hace falta 403).
     *
     * @return array{agentIds: ?\Illuminate\Support\Collection, modo: string, puedeVerArea: bool, puedeVerTodos: bool, miArea: ?string, areaElegida: ?string}
     */
    private function alcanceInformes(Request $request): array
    {
        $modo = $request->input('modo', 'propias');
        $puedeVerTodos = auth()->user()->hasDirectPermission('interacciones.informes.todosagentes');
        $puedeVerArea = $puedeVerTodos || auth()->user()->hasDirectPermission('interacciones.informes.area');
        $miArea = $this->areaDelUsuario(Auth::id());
        $areaElegida = null;

        if ($modo === 'todos' && $puedeVerTodos) {
            $agentIds = null; // sin restricción
        } elseif ($modo === 'area' && $puedeVerArea) {
            $areaElegida = ($puedeVerTodos && $request->filled('area_id')) ? $request->input('area_id') : $miArea;
            $agentIds = $areaElegida
                ? User::whereHas('roles', fn ($q) => $q->where('area', $areaElegida))->pluck('id')
                : collect([Auth::id()]);
        } else {
            $agentIds = collect([Auth::id()]);
        }

        // Drill-down a un agente puntual (compatible con el filtro "Agente" que ya existía).
        if ($request->filled('agent_id')) {
            $agentIds = $agentIds === null
                ? collect([$request->input('agent_id')])
                : $agentIds->filter(fn ($id) => $id == $request->input('agent_id'))->values();
        }

        return [
            'agentIds' => $agentIds,
            'modo' => $modo,
            'puedeVerArea' => $puedeVerArea,
            'puedeVerTodos' => $puedeVerTodos,
            'miArea' => $miArea,
            'areaElegida' => $areaElegida,
        ];
    }

    public function report(Request $request)
    {
        ['agentIds' => $agentIds, 'modo' => $modo, 'puedeVerArea' => $puedeVerArea, 'puedeVerTodos' => $puedeVerTodos, 'miArea' => $miArea]
            = $this->alcanceInformes($request);
        // $filtroAgente se conserva para la selección del <select> "Agente" en la vista (marca
        // la opción elegida) y para replicarlo en las subconsultas de seguimientos más abajo.
        $filtroAgente = $request->input('agent_id');

        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());

        $filtroDistrito = $request->input('distrito_id');
        $filtroLinea = $request->input('linea_id');

        $filtroCliente = $request->input('client_id');

        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();

        // 2. Query Base para interacciones dentro del rango
        $baseQuery = Interaction::whereBetween('interaction_date', [$start, $end]);

        // Aplicar Filtro de Distrito (A través del cliente) si existe
        if ($filtroDistrito) {
            $baseQuery->whereHas('client', function ($q) use ($filtroDistrito) {
                $q->where('cod_dist', $filtroDistrito);
            });
        }

        // Aplicar Filtro de Línea de Crédito si existe.
        // id_linea_de_obligacion se guarda como arreglo JSON de ids en texto (ej. '["8"]', o
        // '["8","12"]' cuando una interacción toca varias líneas — pasa en ~7% de los casos).
        // Antes comparaba la columna completa contra un solo id ('where(...,$filtroLinea)'):
        // eso compara '["8"]' = '8' a nivel de texto, que nunca es igual — el filtro no
        // encontraba nada, en ningún reporte, para ningún valor. whereJsonContains() sí mira
        // dentro del arreglo. (string) porque los ids se guardan como texto, no como número.
        if ($filtroLinea) {
            $baseQuery->whereJsonContains('id_linea_de_obligacion', (string) $filtroLinea);
        }

        // Alcance por agente/área (ver alcanceInformes arriba). null = sin restricción.
        if ($agentIds !== null) {
            $baseQuery->whereIn('agent_id', $agentIds);
        }

        // NUEVO: Aplicar Filtro de Cliente si existe
        if ($filtroCliente) {
            $baseQuery->where('client_id', $filtroCliente);
        }

        // 3. Cálculos de Tarjetas (KPIs)
        $totalInteracciones = (clone $baseQuery)->count();

        $exitosas = (clone $baseQuery)
            ->whereHas('outcomeRelation', function ($q) {
                $q->where('estado', 1);
            })
            ->count();

        $pendientes = (clone $baseQuery)
            ->whereHas('outcomeRelation', function ($q) {
                $q->where('estado', '!=', 1)->orWhereNull('estado');
            })
            ->count();

        // NUEVO: Se agregaron $filtroAgente y $filtroCliente al use() de la subconsulta
        $vencidas = IntSeguimiento::whereHas('interaction', function ($q) use ($start, $end, $filtroDistrito, $filtroLinea, $agentIds, $filtroCliente) {
            $q->whereBetween('interaction_date', [$start, $end])->whereHas('outcomeRelation', function ($q2) {
                $q2->where('estado', '!=', 1)->orWhereNull('estado');
            });

            // Replicar filtros en seguimientos
            if ($filtroDistrito) {
                $q->whereHas('client', function ($q3) use ($filtroDistrito) {
                    $q3->where('cod_dist', $filtroDistrito);
                });
            }
            if ($filtroLinea) {
                $q->whereJsonContains('id_linea_de_obligacion', (string) $filtroLinea);
            }

            // Alcance por agente/área + drill-down puntual (ver alcanceInformes).
            if ($agentIds !== null) {
                $q->whereIn('agent_id', $agentIds);
            }
            if ($filtroCliente) {
                $q->where('client_id', $filtroCliente);
            }
        })
            // Solo el último seguimiento de cada interacción (ver ultimosSeguimientosPendientes()):
            // si una interacción tuvo varios seguimientos en el tiempo, una fecha vieja ya
            // superada por una gestión más reciente no debe seguir contando como vencida.
            ->whereIn('id', DB::table('int_seguimiento')->select(DB::raw('MAX(id)'))->groupBy('id_interaction'))
            ->whereNotNull('next_action_date')
            ->where('next_action_date', '<', Carbon::now())
            ->count();

        $stats = [
            'total' => $totalInteracciones,
            'successful' => $exitosas,
            'pending' => $pendientes,
            'overdue' => $vencidas,
        ];

        // 4. Datos para Gráficos

        // a. Agrupación por Canal
        $canalesData = (clone $baseQuery)->select('interaction_channel', DB::raw('count(*) as total'))->with('channel')->groupBy('interaction_channel')->get();

        $chartCanales = [
            'labels' => $canalesData->map(fn ($item) => $item->channel->name ?? 'Desconocido')->toArray(),
            'data' => $canalesData->pluck('total')->toArray(),
        ];

        // b. Agrupación por Resultado (Outcome)
        $resultadosData = (clone $baseQuery)->select('outcome', DB::raw('count(*) as total'))->with('outcomeRelation')->groupBy('outcome')->get();

        $chartResultados = [
            'labels' => $resultadosData->map(fn ($item) => $item->outcomeRelation->name ?? 'Sin Estado')->toArray(),
            'data' => $resultadosData->pluck('total')->toArray(),
        ];

        // c. Top 5 Agentes con más interacciones
        $agentesData = (clone $baseQuery)->select('agent_id', DB::raw('count(*) as total'))->with('agent')->groupBy('agent_id')->orderByDesc('total')->limit(5)->get();

        $chartAgentes = [
            'labels' => $agentesData->map(fn ($item) => $item->agent->name ?? 'Sin Agente')->toArray(),
            'data' => $agentesData->pluck('total')->toArray(),
        ];

        // d. Top 5 Clientes
        $clientesData = (clone $baseQuery)->select('client_id', DB::raw('count(*) as total'))->with('client')->groupBy('client_id')->orderByDesc('total')->limit(5)->get();

        $chartClientes = [
            'labels' => $clientesData->map(fn ($item) => $item->client->nom_ter ?? 'Cliente '.$item->client_id)->toArray(),
            'data' => $clientesData->pluck('total')->toArray(),
        ];

        // e. Agrupación por Línea de Crédito (CORREGIDO PARA USAR CACHÉ Y EVITAR ERROR NULL)
        $allLineas = Cache::remember('all_lineas_list', 3600, fn () => LineaCredito::pluck('nombre', 'id'));

        $lineasData = (clone $baseQuery)
            ->select('id_linea_de_obligacion', DB::raw('count(*) as total'))
            ->groupBy('id_linea_de_obligacion')
            ->orderByDesc('total')
            ->limit(5) // Top 5 para el gráfico
            ->get();

        $chartLineas = [
            'labels' => $lineasData->map(function ($item) use ($allLineas) {
                // Verificamos si es string JSON y lo decodificamos, o si ya es array
                $ids = is_array($item->id_linea_de_obligacion) 
                        ? $item->id_linea_de_obligacion 
                        : json_decode($item->id_linea_de_obligacion, true);
                
                $primerId = $ids[0] ?? null;
                
                return $primerId && isset($allLineas[$primerId]) ? $allLineas[$primerId] : 'Sin Línea';
            })->toArray(),
            'data' => $lineasData->pluck('total')->toArray(),
        ];

        // f. Agrupación por Distrito (Relación anidada)
        $distritosInteracciones = (clone $baseQuery)->with(['client.distrito'])->get();

        $distritosAgrupados = $distritosInteracciones
            ->groupBy(function ($item) {
                // CORRECCIÓN AQUÍ: Usamos NOM_DIST según tu modelo MaeDistritos
                return optional(optional($item->client)->distrito)->NOM_DIST ?? 'Sin Distrito';
            })
            ->map(function ($row) {
                return $row->count();
            })
            ->sortByDesc(function ($count) {
                return $count;
            })
            ->take(5);

        $chartDistritos = [
            'labels' => $distritosAgrupados->keys()->toArray(),
            'data' => $distritosAgrupados->values()->toArray(),
        ];

        // g. (NUEVO) Top 5 Agentes por Seguimientos
        $seguimientosAgentesData = IntSeguimiento::whereHas('interaction', function ($q) use ($start, $end, $filtroDistrito, $filtroLinea, $agentIds, $filtroCliente) {
            $q->whereBetween('interaction_date', [$start, $end]);
            if ($filtroDistrito) {
                $q->whereHas('client', function ($q3) use ($filtroDistrito) {
                    $q3->where('cod_dist', $filtroDistrito);
                });
            }
            if ($filtroLinea) {
                $q->whereJsonContains('id_linea_de_obligacion', (string) $filtroLinea);
            }
            if ($agentIds !== null) {
                $q->whereIn('agent_id', $agentIds);
            }
            if ($filtroCliente) {
                $q->where('client_id', $filtroCliente);
            }
        })
            ->select('agent_id', DB::raw('count(*) as total'))
            ->with('creator')
            ->groupBy('agent_id')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $chartSeguimientosAgentes = [
            'labels' => $seguimientosAgentesData->map(fn ($item) => optional($item->creator)->name ?? 'Sin Agente')->toArray(),
            'data' => $seguimientosAgentesData->pluck('total')->toArray(),
        ];

        $accionesAgentes = IntSeguimiento::select('agent_id', DB::raw('SUM(CASE WHEN next_action_date >= NOW() THEN 1 ELSE 0 END) as pendientes'), DB::raw('SUM(CASE WHEN next_action_date < NOW() THEN 1 ELSE 0 END) as vencidas'))
            ->whereNotNull('next_action_date')
            ->whereHas('interaction', function ($q) use ($start, $end, $filtroDistrito, $filtroLinea, $agentIds, $filtroCliente) {
                $q->whereBetween('interaction_date', [$start, $end])->whereHas('outcomeRelation', function ($q2) {
                    $q2->where('estado', '!=', 1)->orWhereNull('estado');
                });

                if ($filtroDistrito) {
                    $q->whereHas('client', function ($q3) use ($filtroDistrito) {
                        $q3->where('cod_dist', $filtroDistrito);
                    });
                }

                if ($filtroLinea) {
                    $q->whereJsonContains('id_linea_de_obligacion', (string) $filtroLinea);
                }

                if ($agentIds !== null) {
                    $q->whereIn('agent_id', $agentIds);
                }

                if ($filtroCliente) {
                    $q->where('client_id', $filtroCliente);
                }
            })
            ->with('creator')
            ->groupBy('agent_id')
            ->orderByDesc('vencidas')
            ->get();
            
        $chartAccionesAgentes = [
            'labels' => $accionesAgentes->map(fn ($item) => optional($item->creator)->name ?? 'Sin Agente')->toArray(),
            'pendientes' => $accionesAgentes->pluck('pendientes')->toArray(),
            'vencidas' => $accionesAgentes->pluck('vencidas')->toArray(),
        ];

        // h. (NUEVO) Indicadores por Área — solo tiene sentido cuando el alcance cubre más de una
        // (modo=todos, o modo=area con listado.todos eligiendo "Todas"); con una sola área en
        // alcance simplemente sale una barra. Se resuelve el área de cada agente en un solo lote
        // (no una consulta por agente) con el mismo criterio que areaDelUsuario(): prefiere un
        // rol que SÍ tenga área sobre uno que no, para los perfiles legado con más de uno.
        $porAgenteData = (clone $baseQuery)->select('agent_id', DB::raw('count(*) as total'))->groupBy('agent_id')->pluck('total', 'agent_id');
        $areaPorAgente = DB::table('actions')
            ->join('roles', 'roles.id', '=', 'actions.role_id')
            ->whereIn('actions.user_id', $porAgenteData->keys())
            ->orderByRaw('roles.area is null')
            ->select('actions.user_id', 'roles.area')
            ->get()
            ->unique('user_id')
            ->pluck('area', 'user_id');

        $totalesPorArea = [];
        foreach ($porAgenteData as $agenteId => $total) {
            $area = $areaPorAgente[$agenteId] ?? null;
            $etiqueta = $area ? strtoupper($area) : 'Sin área';
            $totalesPorArea[$etiqueta] = ($totalesPorArea[$etiqueta] ?? 0) + $total;
        }
        arsort($totalesPorArea);

        $chartAreas = [
            'labels' => array_keys($totalesPorArea),
            'data' => array_values($totalesPorArea),
        ];

        // 5. Listas para los select de Filtro
        // Asegúrate de tener el modelo MaeDistritos importado arriba
        $listDistritos = MaeDistritos::all();
        $listLineas = LineaCredito::all();

        // Selector de Agente: mismo alcance que el informe — sin permiso alguno no tiene caso
        // ofrecer un desplegable (la vista lo oculta), con informes.area se limita a los agentes
        // de la propia área, y con informes.todosagentes a cualquiera con acceso al módulo.
        $listAgentes = collect();
        if ($puedeVerArea) {
            $listAgentes = User::whereHas('permissions', fn ($q) => $q->where('name', 'menu.interacciones'))
                ->when(!$puedeVerTodos, fn ($q) => $q->whereHas('roles', fn ($q2) => $q2->where('area', $miArea)))
                ->select('id', 'name')
                ->orderBy('name')
                ->get();
        }
        // Selector de Área: solo con informes.todosagentes tiene sentido elegir cuál (con
        // informes.area ya está fija en la propia, ver alcanceInformes).
        $listAreas = $puedeVerTodos
            ? DB::table('roles')->whereNotNull('area')->distinct()->orderBy('area')->pluck('area')
            : collect();

        // Límite de 1000 agregado por seguridad de rendimiento si tu base de clientes es muy grande.
        $listClientes = MaeTerceros::select('cod_ter', 'nom_ter')->limit(1000)->get();

        // 6. Retornar Vista
        return view(
            'interactions.reportes.report',
            compact(
                'stats',
                'chartCanales',
                'chartResultados',
                'chartAgentes',
                'chartClientes',
                'chartLineas',
                'chartDistritos',
                'chartSeguimientosAgentes',
                'chartAccionesAgentes',
                'chartAreas',
                'startDate',
                'endDate',
                'filtroDistrito',
                'filtroLinea',
                'filtroAgente',
                'filtroCliente',
                'listDistritos',
                'listLineas',
                'listAgentes',
                'listClientes',
                'listAreas',
                'modo',
                'puedeVerArea',
                'puedeVerTodos',
            ),
        );
    }

    public function reportPdf(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());

        $filtroDistrito = $request->input('distrito_id');
        $filtroLinea = $request->input('linea_id');
        $filtroCliente = $request->input('client_id');

        // Mismo alcance de 3 niveles que report() (propias/área/todos, ver alcanceInformes) —
        // antes esto se validaba solo en la pantalla, no aquí, así que pedir el PDF directo por
        // URL con otro agent_id daba acceso a los datos de cualquier persona.
        ['agentIds' => $agentIds] = $this->alcanceInformes($request);

        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();

        $baseQuery = Interaction::whereBetween('interaction_date', [$start, $end]);

        if ($filtroDistrito) {
            $baseQuery->whereHas('client', function ($q) use ($filtroDistrito) {
                $q->where('cod_dist', $filtroDistrito);
            });
        }
        if ($filtroLinea) {
            $baseQuery->whereJsonContains('id_linea_de_obligacion', (string) $filtroLinea);
        }
        if ($agentIds !== null) {
            $baseQuery->whereIn('agent_id', $agentIds);
        }
        if ($filtroCliente) {
            $baseQuery->where('client_id', $filtroCliente);
        }

        $totalInteracciones = (clone $baseQuery)->count();
        $exitosas = (clone $baseQuery)
            ->whereHas('outcomeRelation', function ($q) {
                $q->where('estado', 1);
            })
            ->count();
        $pendientes = (clone $baseQuery)
            ->whereHas('outcomeRelation', function ($q) {
                $q->where('estado', '!=', 1)->orWhereNull('estado');
            })
            ->count();

        $vencidas = IntSeguimiento::whereHas('interaction', function ($q) use ($start, $end, $filtroDistrito, $filtroLinea, $agentIds, $filtroCliente) {
            $q->whereBetween('interaction_date', [$start, $end])->whereHas('outcomeRelation', function ($q2) {
                $q2->where('estado', '!=', 1)->orWhereNull('estado');
            });
            if ($filtroDistrito) {
                $q->whereHas('client', function ($q3) use ($filtroDistrito) {
                    $q3->where('cod_dist', $filtroDistrito);
                });
            }
            if ($filtroLinea) {
                $q->whereJsonContains('id_linea_de_obligacion', (string) $filtroLinea);
            }
            if ($agentIds !== null) {
                $q->whereIn('agent_id', $agentIds);
            }
            if ($filtroCliente) {
                $q->where('client_id', $filtroCliente);
            }
        })
            // Solo el último seguimiento de cada interacción (ver ultimosSeguimientosPendientes()):
            // si una interacción tuvo varios seguimientos en el tiempo, una fecha vieja ya
            // superada por una gestión más reciente no debe seguir contando como vencida.
            ->whereIn('id', DB::table('int_seguimiento')->select(DB::raw('MAX(id)'))->groupBy('id_interaction'))
            ->whereNotNull('next_action_date')
            ->where('next_action_date', '<', Carbon::now())
            ->count();

        $stats = [
            'total' => $totalInteracciones,
            'successful' => $exitosas,
            'pending' => $pendientes,
            'overdue' => $vencidas,
        ];

        // --- Gráficos ---

        $canalesData = (clone $baseQuery)->select('interaction_channel', DB::raw('count(*) as total'))->with('channel')->groupBy('interaction_channel')->get();
        $chartCanales = ['labels' => $canalesData->map(fn ($item) => $item->channel->name ?? 'Desconocido')->toArray(), 'data' => $canalesData->pluck('total')->toArray()];

        $resultadosData = (clone $baseQuery)->select('outcome', DB::raw('count(*) as total'))->with('outcomeRelation')->groupBy('outcome')->get();
        $chartResultados = ['labels' => $resultadosData->map(fn ($item) => $item->outcomeRelation->name ?? 'Sin Estado')->toArray(), 'data' => $resultadosData->pluck('total')->toArray()];

        // CORRECCIÓN LÍNEA DE CRÉDITO (Punto del error)
        $lineasData = (clone $baseQuery)
            ->select(
                DB::raw('JSON_UNQUOTE(JSON_EXTRACT(id_linea_de_obligacion, "$[0]")) as primer_id'),
                DB::raw('count(*) as total'),
            )
            ->groupBy('primer_id')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        // Obtenemos los nombres de las líneas manualmente para evitar el error de array key 0
        $idsLineas = $lineasData->pluck('primer_id')->filter()->toArray();
        $nombresLineasMap = LineaCredito::whereIn('id', $idsLineas)->pluck('nombre', 'id');

        $chartLineas = [
            'labels' => $lineasData->map(function ($item) use ($nombresLineasMap) {
                return $nombresLineasMap[$item->primer_id] ?? 'Sin Línea';
            })->toArray(),
            'data' => $lineasData->pluck('total')->toArray(),
        ];

        $clientesData = (clone $baseQuery)->select('client_id', DB::raw('count(*) as total'))->with('client')->groupBy('client_id')->orderByDesc('total')->limit(5)->get();
        $chartClientes = ['labels' => $clientesData->map(fn ($item) => $item->client->nom_ter ?? 'Cliente '.$item->client_id)->toArray(), 'data' => $clientesData->pluck('total')->toArray()];

        // AUDITORÍA POR AGENTE
        $agentesList = (clone $baseQuery)->select('agent_id')->distinct()->pluck('agent_id');
        $agentesAuditoria = collect();

        // Área de cada agente, resuelta en un solo lote (mismo criterio que areaDelUsuario():
        // prefiere un rol que SÍ tenga área sobre uno que no, para los perfiles legado).
        $areaPorAgente = DB::table('actions')
            ->join('roles', 'roles.id', '=', 'actions.role_id')
            ->whereIn('actions.user_id', $agentesList)
            ->orderByRaw('roles.area is null')
            ->select('actions.user_id', 'roles.area')
            ->get()
            ->unique('user_id')
            ->pluck('area', 'user_id');

        foreach ($agentesList as $agente_id) {
            $qAgente = (clone $baseQuery)->where('agent_id', $agente_id);
            $totalAgente = (clone $qAgente)->count();

            // NUEVO: Sumar el tiempo total de las interacciones por agente
            $tiempoTotalAgente = (clone $qAgente)->sum('duration');

            $exitosasAgente = (clone $qAgente)->whereHas('outcomeRelation', fn ($q) => $q->where('estado', 1))->count();
            $pendientesAgente = (clone $qAgente)->whereHas('outcomeRelation', fn ($q) => $q->where('estado', '!=', 1)->orWhereNull('estado'))->count();

            $vencidasAgente = IntSeguimiento::whereHas('interaction', function ($q) use ($start, $end, $filtroDistrito, $filtroLinea, $filtroCliente, $agente_id) {
                $q->whereBetween('interaction_date', [$start, $end])
                    ->where('agent_id', $agente_id)
                    ->whereHas('outcomeRelation', fn ($q2) => $q2->where('estado', '!=', 1)->orWhereNull('estado'));

                if ($filtroDistrito) {
                    $q->whereHas('client', fn ($q3) => $q3->where('cod_dist', $filtroDistrito));
                }
                if ($filtroLinea) {
                    $q->whereJsonContains('id_linea_de_obligacion', (string) $filtroLinea);
                }
                if ($filtroCliente) {
                    $q->where('client_id', $filtroCliente);
                }
            })
                ->whereNotNull('next_action_date')
                ->where('next_action_date', '<', Carbon::now())
                ->count();

            $seguimientosAgente = IntSeguimiento::whereHas('interaction', function ($q) use ($start, $end, $filtroDistrito, $filtroLinea, $filtroCliente, $agente_id) {
                $q->whereBetween('interaction_date', [$start, $end])->where('agent_id', $agente_id);
                if ($filtroDistrito) {
                    $q->whereHas('client', fn ($q3) => $q3->where('cod_dist', $filtroDistrito));
                }
                if ($filtroLinea) {
                    $q->whereJsonContains('id_linea_de_obligacion', (string) $filtroLinea);
                }
                if ($filtroCliente) {
                    $q->where('client_id', $filtroCliente);
                }
            })->count();

            // CORRECCIÓN AQUÍ: Evitamos que rompa si no hay agente asociado
            $agenteRelacion = User::find($agente_id);
            $nombreAgente = $agenteRelacion->name ?? 'Sin Agente';

            $efectividad = $totalAgente > 0 ? round(($exitosasAgente / $totalAgente) * 100, 1) : 0;
            $areaAgente = $areaPorAgente[$agente_id] ?? null;

            $agentesAuditoria->push((object) [
                'nombre' => $nombreAgente,
                'area' => $areaAgente ? strtoupper($areaAgente) : 'Sin área',
                'total' => $totalAgente,
                'exitosas' => $exitosasAgente,
                'pendientes' => $pendientesAgente,
                'vencidas' => $vencidasAgente,
                'seguimientos' => $seguimientosAgente,
                'efectividad' => $efectividad,
                'tiempo_total' => $tiempoTotalAgente, // NUEVO: Pasamos la suma de duraciones
            ]);
        }

        $agentesAuditoria = $agentesAuditoria->sortByDesc('total')->values();

        // NUEVO: mismos indicadores que por agente, pero sumados por área — para comparar
        // Cartera vs Seguros (o las que se activen) de un vistazo, sin tener que sumar filas a
        // mano en la tabla de agentes.
        $areasAuditoria = $agentesAuditoria
            ->groupBy('area')
            ->map(function ($filas, $area) {
                $total = $filas->sum('total');

                return (object) [
                    'area' => $area,
                    'total' => $total,
                    'exitosas' => $filas->sum('exitosas'),
                    'pendientes' => $filas->sum('pendientes'),
                    'vencidas' => $filas->sum('vencidas'),
                    'seguimientos' => $filas->sum('seguimientos'),
                    'agentes' => $filas->count(),
                    'efectividad' => $total > 0 ? round(($filas->sum('exitosas') / $total) * 100, 1) : 0,
                ];
            })
            ->sortByDesc('total')
            ->values();

        $mejorAgente = $agentesAuditoria->first();
        $agenteMasEfectivo = $agentesAuditoria->where('total', '>', 5)->sortByDesc('efectividad')->first();
        $tasaGlobal = $totalInteracciones > 0 ? round(($exitosas / $totalInteracciones) * 100, 1) : 0;

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('interactions.reportes.pdf', compact('stats', 'chartCanales', 'chartResultados', 'chartClientes', 'chartLineas', 'agentesAuditoria', 'areasAuditoria', 'startDate', 'endDate', 'mejorAgente', 'agenteMasEfectivo', 'tasaGlobal'));

        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('informe_auditoria_'.Carbon::now()->format('Ymd_Hi').'.pdf');
    }

    /**
     * Ids del ÚLTIMO seguimiento de cada interacción (por orden de creación) que todavía tiene
     * una fecha de próxima acción. "Último" importa: una interacción con varios seguimientos en
     * el tiempo (ej. tres llamadas) solo debe contar una vez, según su seguimiento más reciente
     * — no según uno viejo que ya quedó superado por una gestión posterior.
     */
    private function ultimosSeguimientosPendientes()
    {
        $ultimos = DB::table('int_seguimiento')->select(DB::raw('MAX(id) as id'))->groupBy('id_interaction');

        return IntSeguimiento::whereIn('id', $ultimos)->whereNotNull('next_action_date');
    }

    /**
     * El área de un agente para Interacciones: la de su perfil (roles.area en la Matriz de
     * Permisos), no la de Recursos Humanos (cargo/gdo_area) — esa depende de tener cédula y un
     * cargo documentado, y hoy varios agentes activos no lo tienen (Magnolia Peña, por ejemplo).
     * El perfil, en cambio, ya lo tiene asignado todo el que usa la aplicación.
     * Requiere 'roles' precargado en el modelo (with('agent.roles')). Si el usuario aún tiene más
     * de un perfil (caso legado, antes de "un perfil por usuario"), se prefiere uno que sí tenga
     * área asignada en vez del primero al azar.
     */
    private function perfilPrincipal(?User $agente)
    {
        if (!$agente || !$agente->relationLoaded('roles')) {
            return null;
        }

        return $agente->roles->sortByDesc(fn ($r) => $r->area ? 1 : 0)->first();
    }

    /**
     * El área del perfil de un usuario, consultada directa (sin depender de que 'roles' venga
     * precargado) — para usarla en el alcance de seguridad de index(). Igual que
     * perfilPrincipal(): si tiene más de un perfil (caso legado), prefiere uno con área asignada.
     */
    private function areaDelUsuario(int $userId): ?string
    {
        return DB::table('actions')
            ->join('roles', 'roles.id', '=', 'actions.role_id')
            ->where('actions.user_id', $userId)
            ->orderByRaw('roles.area is null') // los que SÍ tienen área, primero
            ->value('roles.area');
    }

    /**
     * Los 6 contadores de las pestañas (Vencidos/Pendientes/Hoy/Próximos/Total; Exitosos aparte,
     * ver abajo). $baseQuery ya debe traer aplicado el alcance (propias/área/todos) y, si aplica,
     * el filtro de usuario/área elegido — se usa tal cual, clonándolo en cada conteo.
     */
    private function calcularStats($baseQuery): array
    {
        $outcomesData = IntOutcome::select('id', 'estado')->get();
        $successfulOutcomeIds = $outcomesData->where('estado', 1)->pluck('id')->toArray();
        $pendingOutcomeIds = $outcomesData->where('estado', 0)->pluck('id')->toArray();

        $stats = (clone $baseQuery)->selectRaw('
            COUNT(*) as total,
            SUM(CASE WHEN outcome IN ('.implode(',', $pendingOutcomeIds ?: [0]).') THEN 1 ELSE 0 END) as pending,
            SUM(CASE WHEN DATE(interaction_date) = CURDATE() THEN 1 ELSE 0 END) as today
        ')->first()->toArray();

        // Exitosos es SIEMPRE personal (lo que YO gestioné), sin importar el permiso "ver todos"
        // ni el filtro de usuario/área — es un tablero de logro propio, no un listado general. El
        // último mes por defecto, mismo criterio que usa la pestaña.
        $stats['successful'] = Interaction::where('agent_id', Auth::id())
            ->whereIn('outcome', $successfulOutcomeIds ?: [0])
            ->whereBetween('interaction_date', [now()->subDays(30)->startOfDay(), now()->endOfDay()])
            ->count();

        $abiertaScope = fn ($q) => $q->where('estado', '!=', 1)->orWhereNull('estado');

        $stats['overdue'] = (clone $baseQuery)
            ->whereIn('id', (clone $this->ultimosSeguimientosPendientes())->where('next_action_date', '<', now())->select('id_interaction'))
            ->whereHas('outcomeRelation', $abiertaScope)
            ->count();

        $stats['upcoming'] = (clone $baseQuery)
            ->whereIn('id', (clone $this->ultimosSeguimientosPendientes())->whereBetween('next_action_date', [now(), now()->copy()->addDays(3)])->select('id_interaction'))
            ->whereHas('outcomeRelation', $abiertaScope)
            ->count();

        return $stats;
    }

    /** "Vencido hace 3 días" / "Vence hoy" / "Vence en 2 días", para la lista de pendientes. */
    private function textoDiasRestantes(Carbon $fecha): string
    {
        if ($fecha->isPast() && ! $fecha->isToday()) {
            $dias = (int) floor($fecha->diffInDays(now()));

            return 'Vencido hace '.$dias.' día'.($dias == 1 ? '' : 's');
        }
        if ($fecha->isToday()) {
            return 'Vence hoy';
        }
        $dias = (int) ceil(now()->diffInDays($fecha));

        return 'Vence en '.$dias.' día'.($dias == 1 ? '' : 's');
    }

    /**
     * Alcance en 3 niveles para cualquier pantalla de Interacciones (Listado y Auditoría),
     * elegido a propósito por la persona — nunca automático según su permiso: aunque tenga
     * listado.area o listado.todos, por defecto ("propias", sin el parámetro modo) ve solo lo
     * suyo. Para ver más tiene que elegirlo activamente en la pantalla (los botones "Ver").
     *  - modo=todos, con interacciones.listado.todos: sin restricción, cualquier área.
     *  - modo=area, con listado.area (o listado.todos): solo agentes de SU MISMA área (ej.
     *    segurosadmon ve el equipo de Seguros, no el de Cartera). Con listado.todos se puede
     *    elegir explícitamente OTRA área vía ?area_id=.
     *  - modo=propias, o cualquier otro caso (incluido pedir un modo sin el permiso): solo lo
     *    propio.
     *
     * @return array{baseQuery: \Illuminate\Database\Eloquent\Builder, modo: string, puedeVerArea: bool, puedeVerTodos: bool, miArea: ?string}
     */
    private function alcanceInteracciones(Request $request): array
    {
        $baseQuery = Interaction::query();

        $modo = $request->input('modo', 'propias');
        $puedeVerTodos = auth()->user()->hasDirectPermission('interacciones.listado.todos');
        $puedeVerArea = $puedeVerTodos || auth()->user()->hasDirectPermission('interacciones.listado.area');
        $miAreaParaFiltro = null;

        if ($modo === 'todos' && $puedeVerTodos) {
            // sin restricción
        } elseif ($modo === 'area' && $puedeVerArea) {
            $miAreaParaFiltro = ($puedeVerTodos && $request->filled('area_id'))
                ? $request->input('area_id')
                : $this->areaDelUsuario(Auth::id());
            if ($miAreaParaFiltro) {
                $idsDeMiArea = User::whereHas('roles', fn ($q) => $q->where('area', $miAreaParaFiltro))->pluck('id');
                $baseQuery->where(fn ($q) => $q->whereIn('agent_id', $idsDeMiArea)->orWhereIn('id_user_asignacion', $idsDeMiArea));
            } else {
                // Su perfil no tiene área asignada en la Matriz: no hay "mi área" que ampliar.
                $baseQuery->where(fn ($q) => $q->where('agent_id', Auth::id())->orWhere('id_user_asignacion', Auth::id()));
            }
        } else {
            $baseQuery->where(fn ($q) => $q->where('agent_id', Auth::id())->orWhere('id_user_asignacion', Auth::id()));
        }

        return [
            'baseQuery' => $baseQuery,
            'modo' => $modo,
            'puedeVerArea' => $puedeVerArea,
            'puedeVerTodos' => $puedeVerTodos,
            'miArea' => $miAreaParaFiltro,
        ];
    }

    /**
     * Una fila de la tabla de interacciones, en el formato que espera el Javascript — compartido
     * por Listado (index) y Auditoría. $proximasPorInteraccion solo aplica a Vencidos/Próximos
     * (agenda); en Auditoría llega vacía y esos campos simplemente quedan null.
     */
    private function formatearFilaInteraccion($item, $allLineas, $proximasPorInteraccion): array
    {
        $lineasIds = is_array($item->id_linea_de_obligacion) ? $item->id_linea_de_obligacion : [];
        $nombresLineas = [];
        for ($i = 0; $i < 5; $i++) {
            $nombresLineas[] = isset($lineasIds[$i]) ? ($allLineas[$lineasIds[$i]] ?? '') : '';
        }

        return [
            'id' => $item->id,
            'fecha' => optional($item->interaction_date)->format('d/m/Y H:i'),
            'duracion' => floor($item->duration / 60).'m '.($item->duration % 60).'s',
            'cliente_nombre' => $item->client->nom_ter ?? '—',
            'cliente_cc' => $item->client_id ?? '—',
            'distrito' => $item->client->distrito->NOM_DIST ?? '—',
            'agente' => $item->agent->name ?? '—',
            'agente_area' => optional($this->perfilPrincipal($item->agent))->area
                ? strtoupper($this->perfilPrincipal($item->agent)->area)
                : optional($this->perfilPrincipal($item->agent))->name,
            'agente_cargo' => $item->agent->cargoRelation->nombre_cargo ?? '',
            'canal' => $item->channel->name ?? '—',
            'motivo' => $item->type->name ?? 'N/A',
            'resultado' => $item->outcomeRelation->name ?? ' ',
            'outcome_val' => $item->outcome,
            'lineas_array' => array_filter($nombresLineas), // Solo las que tienen datos
            'linea_1' => $nombresLineas[0],
            'linea_2' => $nombresLineas[1],
            'linea_3' => $nombresLineas[2],
            'linea_4' => $nombresLineas[3],
            'linea_5' => $nombresLineas[4],
            // Modal datos extra
            'asignado' => $item->usuarioAsignado->name ?? '—',
            'llamante_nombre' => $item->nombre_quien_llama ?? '—',
            'llamante_cedula' => $item->cedula_quien_llama ?? '—',
            'llamante_celular' => $item->celular_quien_llama ?? '—',
            'llamante_parentesco' => $item->parentesco_quien_llama ?? '—',
            'notas' => $item->notes ?? 'Sin notas.',
            'archivo' => !empty($item->attachment_urls) ? $item->getFile($item->attachment_urls) : null,

            // Solo llenos en las pestañas Vencidos/Próximos (ver $proximasPorInteraccion arriba)
            'proxima_accion' => optional(optional($proximasPorInteraccion->get($item->id))->nextAction)->name,
            'proxima_fecha' => optional(optional($proximasPorInteraccion->get($item->id))->next_action_date)->format('d/m/Y H:i'),
            'proxima_notas' => optional($proximasPorInteraccion->get($item->id))->next_action_notes,
            'proxima_texto' => optional($proximasPorInteraccion->get($item->id))->next_action_date
                ? $this->textoDiasRestantes($proximasPorInteraccion->get($item->id)->next_action_date)
                : null,
            'proxima_vencida' => optional($proximasPorInteraccion->get($item->id))->next_action_date
                ? $proximasPorInteraccion->get($item->id)->next_action_date->isPast() && !$proximasPorInteraccion->get($item->id)->next_action_date->isToday()
                : false,
        ];
    }

    /**
     * Muestra la lista de interacciones con filtros y búsqueda.
     */
    public function index(Request $request)
    {
        ['baseQuery' => $baseQuery, 'modo' => $modo, 'puedeVerArea' => $puedeVerArea, 'puedeVerTodos' => $puedeVerTodos]
            = $this->alcanceInteracciones($request);

        // 1. Estadísticas de las pestañas: solo tienen sentido en la carga de la página completa
        // (las pestañas/paginación/búsqueda llegan por AJAX y no usan $stats). Antes se calculaban
        // siempre, así que cada clic o cada tecla del buscador repetía estas 6 consultas de balde.
        $stats = [];
        if (!$request->ajax()) {
            $stats = $this->calcularStats($baseQuery);
        }

        // 2. Catálogos en Caché
        $allLineas = Cache::remember('all_lineas_list', 3600, fn () => LineaCredito::pluck('nombre', 'id'));
        $channels = Cache::remember('cat_channels', 86400, fn () => IntChannel::orderBy('name')->pluck('name', 'id'));
        $outcomes = Cache::remember('cat_outcomes', 86400, fn () => IntOutcome::orderBy('name')->pluck('name', 'id'));

        // ==========================================
        // 3. RESPUESTA AJAX PARA DATATABLES (SERVER-SIDE)
        // ==========================================
        if ($request->ajax()) {
            // (clone $baseQuery), no Interaction::query() nueva: $baseQuery ya trae la seguridad
            // por agente (solo lo propio, sin el permiso "ver todos"). Antes esta tabla arrancaba
            // de cero y no la heredaba — cualquiera con acceso al módulo veía TODAS las
            // interacciones de TODOS los agentes en las 6 pestañas, tuviera o no el permiso.
            $query = (clone $baseQuery)->with(['client.distrito', 'agent.cargoRelation', 'agent.roles', 'channel', 'type', 'outcomeRelation', 'usuarioAsignado']);

            // A) Filtro por pestaña activa
            $tab = $request->input('tab', 'all');
            if ($tab === 'success') {
                // Exitosos es siempre personal (ver comentario en $stats['successful']), incluso
                // para quien sí tiene "ver todos los agentes".
                $query->where('agent_id', Auth::id())->whereHas('outcomeRelation', fn($q) => $q->where('estado', 1));
            } elseif ($tab === 'pending') {
                $query->whereHas('outcomeRelation', fn($q) => $q->where('estado', '!=', 1)->orWhereNull('estado'));
            } elseif ($tab === 'today') {
                $query->whereDate('interaction_date', now());
            } elseif ($tab === 'overdue') {
                // Subconsulta SQL (no ->pluck(), que la resuelve en PHP y la reinyecta como una
                // lista larga de literales): un solo viaje a la base en vez de dos.
                $query->whereIn('id', $this->ultimosSeguimientosPendientes()->where('next_action_date', '<', now())->select('id_interaction'))
                      ->whereHas('outcomeRelation', fn($q) => $q->where('estado', '!=', 1)->orWhereNull('estado'));
            } elseif ($tab === 'upcoming') {
                $query->whereIn('id', $this->ultimosSeguimientosPendientes()->whereBetween('next_action_date', [now(), now()->copy()->addDays(3)])->select('id_interaction'))
                      ->whereHas('outcomeRelation', fn($q) => $q->where('estado', '!=', 1)->orWhereNull('estado'));
            }

            // B) Filtros del usuario (Buscador general)
            // DataTables envía la búsqueda en $request->input('search.value')
            if ($request->filled('search.value')) {
                $search = "%{$request->input('search.value')}%";
                $query->where(function ($q) use ($search) {
                    $q->where('notes', 'LIKE', $search)
                      ->orWhere('nombre_quien_llama', 'LIKE', $search)
                      ->orWhere('id', 'LIKE', $search)
                      ->orWhere('client_id', 'LIKE', $search)
                      ->orWhereHas('client', fn($sq) => $sq->where('nom_ter', 'LIKE', $search))
                      ->orWhereHas('agent', fn($sq) => $sq->where('name', 'LIKE', $search));
                });
            }

            // Fechas personalizadas
            if ($request->filled('start_date')) $query->where('interaction_date', '>=', $request->start_date.' 00:00:00');
            if ($request->filled('end_date')) $query->where('interaction_date', '<=', $request->end_date.' 23:59:59');

            // C) Totales para la paginación
            $totalRecords = (clone $baseQuery)->count();
            $filteredRecords = $query->count();

            // D) Paginación y Orden (DataTables envía start y length)
            $start = $request->input('start', 0);
            $length = $request->input('length', 20);
            $query->orderBy('id', 'desc'); // Orden fijo por ahora

            $data = $query->skip($start)->take($length)->get();

            // E) Para Vencidos/Próximos: la fecha, el tipo y las notas de la próxima acción, del
            // único seguimiento que ya filtramos arriba (una sola consulta extra sobre la página
            // actual, ≤20 filas — no una por interacción).
            $proximasPorInteraccion = collect();
            if (in_array($tab, ['overdue', 'upcoming'], true) && $data->isNotEmpty()) {
                $proximasPorInteraccion = IntSeguimiento::whereIn('id_interaction', $data->pluck('id'))
                    ->whereIn('id', DB::table('int_seguimiento')->select(DB::raw('MAX(id)'))->groupBy('id_interaction'))
                    ->with('nextAction')
                    ->get()
                    ->keyBy('id_interaction');
            }

            // F) Formatear datos para el Javascript
            $data->transform(fn ($item) => $this->formatearFilaInteraccion($item, $allLineas, $proximasPorInteraccion));

            return response()->json([
                'draw' => intval($request->input('draw')),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $data
            ]);
        }

        // 4. Si es la carga normal de la vista, YA NO ENVIAMOS $interactions NI $collectionsForTabs
        return view('interactions.index', compact('stats', 'channels', 'outcomes', 'modo', 'puedeVerArea', 'puedeVerTodos'));
    }

    /**
     * "Auditoría": el listado completo de interacciones (antes la pestaña "Todos" de Listado de
     * Interacciones), en su propia pantalla para no saturar esa vista — que se queda solo con la
     * agenda (Vencidos/Pendientes/Hoy/Próximos/Mis Exitosos). Sin el botón "Ver" del resto del
     * módulo: aquí Área es un filtro más, arriba con los demás, y viene predeterminado en la
     * PROPIA área de quien entra (nunca "todas" sin elegirlo) — quien tiene listado.todos puede
     * cambiarlo a otra o a "Todas"; quien solo tiene listado.area se queda fijo en la suya; quien
     * no tiene ninguno de los dos permisos ve únicamente lo propio, sin el filtro.
     */
    public function auditoria(Request $request)
    {
        $puedeVerTodos = auth()->user()->hasDirectPermission('interacciones.listado.todos');
        $puedeVerArea = $puedeVerTodos || auth()->user()->hasDirectPermission('interacciones.listado.area');
        $miArea = $this->areaDelUsuario(Auth::id());

        $baseQuery = Interaction::query();
        if ($puedeVerArea) {
            // area_id solo lo puede tocar quien tiene listado.todos (el selector "Área" ni
            // siquiera se renderiza para los demás, ver la vista) — '' es la opción real "Todas"
            // que esa persona eligió a propósito; si el parámetro no llega en absoluto (primera
            // carga, o alguien con solo listado.area) se asume siempre la propia área.
            $areaElegida = ($puedeVerTodos && $request->has('area_id'))
                ? ($request->input('area_id') ?: null)
                : $miArea;

            if ($areaElegida) {
                $idsDelArea = User::whereHas('roles', fn ($q) => $q->where('area', $areaElegida))->pluck('id');
                $baseQuery->where(fn ($q) => $q->whereIn('agent_id', $idsDelArea)->orWhereIn('id_user_asignacion', $idsDelArea));
            } elseif (!$puedeVerTodos) {
                // listado.area sin área propia asignada en su perfil: no hay "la suya" que
                // mostrar, cae a lo propio en vez de quedar sin restricción.
                $baseQuery->where(fn ($q) => $q->where('agent_id', Auth::id())->orWhere('id_user_asignacion', Auth::id()));
            }
            // Si llega aquí con $areaElegida vacío Y $puedeVerTodos: eligió "Todas" a propósito,
            // sin restricción de área.
        } else {
            $baseQuery->where(fn ($q) => $q->where('agent_id', Auth::id())->orWhere('id_user_asignacion', Auth::id()));
        }

        // Rango de fechas: por defecto el último mes si no llega nada — esta pantalla es para
        // auditar un período puntual, no para recorrer sin querer los 18.000+ registros del
        // histórico completo. Se aplica también sobre $totalRecords (no solo $filteredRecords):
        // "de un total de N" debe reflejar el mismo rango, si no confunde igual que los
        // contadores desactualizados que se arreglaron antes.
        $desde = $request->filled('start_date') ? $request->input('start_date').' 00:00:00' : now()->subDays(30)->startOfDay();
        $hasta = $request->filled('end_date') ? $request->input('end_date').' 23:59:59' : now()->endOfDay();
        $baseQuery->whereBetween('interaction_date', [$desde, $hasta]);

        if ($request->ajax()) {
            $query = (clone $baseQuery)->with(['client.distrito', 'agent.cargoRelation', 'agent.roles', 'channel', 'type', 'outcomeRelation', 'usuarioAsignado']);

            // Filtro fino por usuario (dentro del área ya delimitada arriba) — cliente/distrito/
            // canal/motivo/resultado, que en Listado de Interacciones no cabían.
            if ($request->filled('agent_id')) {
                $query->where('agent_id', $request->input('agent_id'));
            }
            if ($request->filled('cliente')) {
                $buscar = $request->input('cliente');
                $query->whereHas('client', fn ($q) => $q->where('nom_ter', 'LIKE', "%{$buscar}%")->orWhere('cod_ter', 'LIKE', "%{$buscar}%"));
            }
            if ($request->filled('distrito_id')) {
                $query->whereHas('client', fn ($q) => $q->where('cod_dist', $request->input('distrito_id')));
            }
            if ($request->filled('canal_id')) {
                $query->where('interaction_channel', $request->input('canal_id'));
            }
            if ($request->filled('motivo_id')) {
                $query->where('interaction_type', $request->input('motivo_id'));
            }
            if ($request->filled('resultado_id')) {
                $query->where('outcome', $request->input('resultado_id'));
            }
            if ($request->filled('search.value')) {
                $search = "%{$request->input('search.value')}%";
                $query->where(function ($q) use ($search) {
                    $q->where('notes', 'LIKE', $search)
                      ->orWhere('nombre_quien_llama', 'LIKE', $search)
                      ->orWhere('id', 'LIKE', $search)
                      ->orWhere('client_id', 'LIKE', $search)
                      ->orWhereHas('client', fn ($sq) => $sq->where('nom_ter', 'LIKE', $search))
                      ->orWhereHas('agent', fn ($sq) => $sq->where('name', 'LIKE', $search));
                });
            }

            $totalRecords = (clone $baseQuery)->count();
            $filteredRecords = $query->count();

            $start = $request->input('start', 0);
            $length = $request->input('length', 20);
            $query->orderBy('id', 'desc');

            $data = $query->skip($start)->take($length)->get();

            $allLineas = Cache::remember('all_lineas_list', 3600, fn () => LineaCredito::pluck('nombre', 'id'));
            $sinProximas = collect(); // Auditoría no muestra "próxima acción" (eso es de la agenda)
            $data->transform(fn ($item) => $this->formatearFilaInteraccion($item, $allLineas, $sinProximas));

            return response()->json([
                'draw' => intval($request->input('draw')),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $data,
            ]);
        }

        // Catálogos para los filtros de la pantalla
        $channels = Cache::remember('cat_channels', 86400, fn () => IntChannel::orderBy('name')->pluck('name', 'id'));
        $outcomes = Cache::remember('cat_outcomes', 86400, fn () => IntOutcome::orderBy('name')->pluck('name', 'id'));
        $types = IntType::orderBy('name')->pluck('name', 'id');
        $distritos = Cache::remember('cat_distritos', 86400, fn () => MaeDistritos::orderBy('NOM_DIST')->pluck('NOM_DIST', 'COD_DIST'));

        $listAgentes = collect();
        $listAreas = collect();
        if ($puedeVerArea) {
            $listAgentes = User::whereHas('permissions', fn ($q) => $q->where('name', 'menu.interacciones'))
                ->when(!$puedeVerTodos, function ($q) use ($miArea) {
                    $q->whereHas('roles', fn ($q2) => $q2->where('area', $miArea));
                })
                ->orderBy('name')->get(['id', 'name']);
            if ($puedeVerTodos) {
                $listAreas = DB::table('roles')->whereNotNull('area')->distinct()->orderBy('area')->pluck('area');
            }
        }

        return view('interactions.auditoria', compact(
            'puedeVerArea', 'puedeVerTodos', 'miArea', 'channels', 'outcomes', 'types', 'distritos', 'listAgentes', 'listAreas'
        ));
    }

    /**
     * Muestra detalles y estadísticas de una interacción.
     */
    public function show(Interaction $interaction)
    {
        // 1. Cargamos todas las relaciones, incluyendo las del timeline y comprobantes
        $interaction->load([
            'agent',
            'client.distrito',
            'channel',
            'type',
            'outcomeRelation',
            //'lineaDeObligacion',
            'usuarioAsignado',
            'comprobantes.banco',
            'seguimientos.outcomeRelation',
            'seguimientos.creator',
            'seguimientos.assignedUser',
            'seguimientos.nextAction',
        ]);

        // 2. Lógica del Gráfico (Rendimiento del Agente)
        // Antes sin límite de fecha: para un agente con miles de interacciones (Laura Nicol
        // tiene 3.104), cada vez que alguien abría CUALQUIERA de sus interacciones se recorría
        // y agrupaba su historial completo de por vida, de nuevo — y crece cada día. La vista
        // no tiene un selector de rango visible (siempre pide 'day'), así que el límite no le
        // quita nada a nadie hoy; solo evita recalcular años de historial en cada clic.
        $agentId = $interaction->agent_id;
        $range = request()->get('range', 'day');
        $desde = match ($range) {
            'month' => now()->copy()->subMonths(24),
            'year' => now()->copy()->subYears(5),
            default => now()->copy()->subDays(60),
        };
        $query = Interaction::where('agent_id', $agentId)->where('interaction_date', '>=', $desde);

        switch ($range) {
            case 'day':
                $query->selectRaw('DATE(interaction_date) as label, COUNT(*) as total')->groupBy('label')->orderBy('label');
                break;
            case 'month':
                $query->selectRaw('DATE_FORMAT(interaction_date, "%Y-%m") as label, COUNT(*) as total')->groupBy('label')->orderBy('label');
                break;
            case 'year':
                $query->selectRaw('YEAR(interaction_date) as label, COUNT(*) as total')->groupBy('label')->orderBy('label');
                break;
        }

        $chartData = $query->get();
        $labels = $chartData->pluck('label');
        $totals = $chartData->pluck('total');

        // 3. Histórico del Cliente
        $clientHistory = collect();
        if ($interaction->client_id) {
            $clientHistory = Interaction::with(['agent', 'channel', 'type', 'outcomeRelation', 'usuarioAsignado'])
                ->where('client_id', $interaction->client_id)
                ->orderByDesc('interaction_date')
                ->get();
        }

        // 4. DATOS PARA EL MODAL
        $outcomes = IntOutcome::all();
        $nextActions = IntNextAction::all();
        $users = User::orderBy('name')->get();

        return view('interactions.show', compact('interaction', 'labels', 'totals', 'range', 'clientHistory', 'outcomes', 'nextActions', 'users'));
    }

    /**
     * Formulario para crear una nueva interacción.
     */
    public function create()
    {
        $interaction = new Interaction;
        $channels = IntChannel::all();
        // Solo los tipos de SU área (+ los compartidos, area=null) — Cartera no debe ver los 30
        // motivos pensados para Seguros de Vida, ni viceversa.
        $miAreaTipos = $this->areaDelUsuario(Auth::id());
        $types = IntType::where(fn ($q) => $q->whereNull('area')->orWhere('area', $miAreaTipos))->orderBy('name')->get();
        $outcomes = IntOutcome::all();
        $nextActions = IntNextAction::all();

        $areas = GdoArea::orderBy('nombre')->pluck('nombre', 'id');
        $cargos = GdoCargo::orderBy('nombre_cargo')->pluck('nombre_cargo', 'id');
        $lineasCredito = LineaCredito::orderBy('nombre')->pluck('nombre', 'id');

        $agente = Auth::user();
        $cargoAgente = null;
        $idCargoAgente = null;
        $areaAgente = null;
        $idAreaAgente = null;
        $idBanco = ConCuentaBancaria::select('id', 'numero_cuenta', 'banco')->get();
        if ($agente) {
            $cargoAgente = $agente->cargoRelation;
            if ($cargoAgente) {
                $idCargoAgente = $cargoAgente->id;

                $areaAgente = $cargoAgente->gdoArea;
                if ($areaAgente) {
                    $idAreaAgente = $areaAgente->id;
                }
            }
        }

        return view('interactions.create', compact('interaction', 'channels', 'types', 'outcomes', 'nextActions', 'areas', 'cargos', 'lineasCredito', 'idCargoAgente', 'idAreaAgente', 'idBanco'));
    }

    /**
     * Guarda una nueva interacción en la base de datos.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required',
            'interaction_date' => 'required|date',
            'interaction_channel' => 'required',
            'interaction_type' => 'required',
            'outcome' => 'required',
            'notes' => 'nullable|string',
            'next_action_date' => 'nullable|date',
            'next_action_type' => 'nullable',
            'next_action_notes' => 'nullable|string',
            'interaction_url' => 'nullable|url',
            'attachment' => 'nullable|file|mimes:jpeg,png,pdf,jpg,doc,docx|max:10240',
            'cedula_quien_llama' => 'nullable|string|max:50',
            'nombre_quien_llama' => 'nullable|string|max:255',
            'celular_quien_llama' => 'nullable|string|max:50',
            'parentesco_quien_llama' => 'nullable|string|max:50',
            'id_linea_de_obligacion.*' => 'integer',
            'id_user_asignacion' => 'nullable|integer',
            'duration' => 'nullable|integer|min:0',
            'parent_interaction_id' => 'nullable|integer',
            'temp_token' => 'nullable|string|max:255',
        ]);

        $agentId = Auth::id();

        // LÓGICA DE ASIGNACIÓN: 
        // Si no llega un ID en el formulario, se asigna por defecto al agente actual (Yo me encargo)
        $idAsignacion = $request->filled('id_user_asignacion') 
                        ? $validated['id_user_asignacion'] 
                        : $agentId;

        // 1. Crear la interacción
        $interaction = Interaction::create([
            'client_id' => $validated['client_id'],
            'agent_id' => $agentId,
            'interaction_date' => $validated['interaction_date'],
            'interaction_channel' => $validated['interaction_channel'],
            'interaction_type' => $validated['interaction_type'],
            'duration' => $validated['duration'] ?? 0,
            'outcome' => $validated['outcome'],
            'notes' => $validated['notes'] ?? null,
            'parent_interaction_id' => $validated['parent_interaction_id'] ?? null,
            'id_linea_de_obligacion' => $validated['id_linea_de_obligacion'] ?? null,
            'id_user_asignacion' => $idAsignacion, // Usamos la variable segura
            'cedula_quien_llama' => $validated['cedula_quien_llama'] ?? null,
            'nombre_quien_llama' => $validated['nombre_quien_llama'] ?? null,
            'celular_quien_llama' => $validated['celular_quien_llama'] ?? null,
            'parentesco_quien_llama' => $validated['parentesco_quien_llama'] ?? null,
        ]);

        // 2. Procesar comprobantes temporales
        if ($request->filled('temp_token')) {
            CarComprobantePago::where('temp_token', $request->temp_token)
                ->where(function ($query) {
                    $query->where('id_interaction', 0)->orWhereNull('id_interaction');
                })
                ->update([
                    'id_interaction' => $interaction->id,
                    'temp_token' => null, 
                ]);
        }

        // 3. Procesar archivo adjunto
        $rutaArchivo = null;
        if ($request->hasFile('attachment')) {
            $folderPath = "corpentunida/interacciones/evidencia_{$interaction->id}_{$interaction->client_id}";
            $rutaArchivo = $request->file('attachment')->store($folderPath, 's3');
        }

        // 4. Crear el seguimiento
        $interaction->seguimientos()->create([
            'agent_id' => $agentId,
            'id_user_asignacion' => $idAsignacion, // Usamos la variable segura
            'outcome' => $validated['outcome'],
            'next_action_type' => $validated['next_action_type'] ?? 1,
            // Antes, sin fecha elegida, quedaba en now(): la agenda nacía vencida en el mismo
            // instante que se creaba, así que "vencidas" nunca distinguía "no necesita
            // seguimiento" de "sí necesita y ya se atrasó". null aquí significa correctamente
            // "sin próxima acción pendiente" (ver pending() más abajo).
            'next_action_date' => $validated['next_action_date'] ?? null,
            'next_action_notes' => $validated['next_action_notes'] ?? ($validated['notes'] ?? null),
            'interaction_url' => $validated['interaction_url'] ?? null,
            'attachment_urls' => $rutaArchivo,
        ]);

        return redirect()->route('interactions.show', $interaction->id)->with('success', 'Interacción creada exitosamente.');
    }

    /**
     * Formulario para editar una interacción existente.
     */
    public function edit(Interaction $interaction)
    {
        $channels = IntChannel::all();
        // Su área + compartidos, y siempre el tipo actual de la interacción aunque sea de otra
        // área (ej. cambió de perfil desde que se creó) — si no, el formulario "pierde" el valor
        // seleccionado.
        $miAreaTipos = $this->areaDelUsuario(Auth::id());
        $types = IntType::where(fn ($q) => $q->whereNull('area')->orWhere('area', $miAreaTipos)->orWhere('id', $interaction->interaction_type))
            ->orderBy('name')->get();
        $outcomes = IntOutcome::all();
        $nextActions = IntNextAction::all();

        $areas = GdoArea::orderBy('nombre')->pluck('nombre', 'id');
        $cargos = GdoCargo::orderBy('nombre_cargo')->pluck('nombre_cargo', 'id');
        $lineasCredito = LineaCredito::orderBy('nombre')->pluck('nombre', 'id');

        $agente = Auth::user();
        $cargoAgente = null;
        $idCargoAgente = null;
        $areaAgente = null;
        $idAreaAgente = null;

        if ($agente) {
            $cargoAgente = $agente->cargoRelation;
            if ($cargoAgente) {
                $idCargoAgente = $cargoAgente->id;
                $areaAgente = $cargoAgente->gdoArea;
                if ($areaAgente) {
                    $idAreaAgente = $areaAgente->id;
                }
            }
        }

        return view('interactions.edit', compact('interaction', 'channels', 'types', 'outcomes', 'nextActions', 'areas', 'cargos', 'lineasCredito', 'idCargoAgente', 'idAreaAgente'));
    }

    /**
     * Actualiza una interacción existente.
     */
    public function update(Request $request, Interaction $interaction)
    {
        $validatedData = $request->validate([
            'client_id' => 'required|exists:MaeTerceros,cod_ter',
            'agent_id' => 'required|exists:users,id',
            'interaction_date' => 'required|date',
            'interaction_channel' => 'required|exists:int_channels,id',
            'interaction_type' => 'required|exists:int_types,id',
            'outcome' => 'required|exists:int_outcomes,id',
            'notes' => 'nullable|string',
            'next_action_date' => 'nullable|date',
            'next_action_type' => 'nullable|exists:int_next_actions,id',
            'next_action_notes' => 'nullable|string',
            'interaction_url' => 'nullable|url',
            'attachment' => 'nullable|file|mimes:jpeg,png,pdf,jpg,doc,docx|max:10240',
            'cedula_quien_llama' => 'nullable|string|max:50',
            'nombre_quien_llama' => 'nullable|string|max:255',
            'celular_quien_llama' => 'nullable|string|max:50',
            'parentesco_quien_llama' => 'nullable|string|max:50',
            'id_linea_de_obligacion' => 'nullable|integer|exists:cre_lineas_creditos,id',
            'id_user_asignacion' => 'nullable|integer|exists:users,id',
            'start_time' => 'nullable|date',
            'duration' => 'nullable|integer|min:0',
            'parent_interaction_id' => 'nullable|integer|exists:interactions,id',
        ]);

        return DB::transaction(function () use ($request, $validatedData, $interaction) {
            $duration = $validatedData['duration'] ?? $interaction->duration;

            // 1. Actualizar Interaction (TABLA 1)
            $interaction->update([
                'client_id' => $validatedData['client_id'],
                'interaction_date' => $validatedData['interaction_date'],
                'interaction_channel' => $validatedData['interaction_channel'],
                'interaction_type' => $validatedData['interaction_type'],
                'duration' => $duration,
                'outcome' => $validatedData['outcome'],
                'notes' => $validatedData['notes'] ?? $interaction->notes,
                'parent_interaction_id' => $validatedData['parent_interaction_id'] ?? $interaction->parent_interaction_id,
                'id_linea_de_obligacion' => $validatedData['id_linea_de_obligacion'] ?? null,
                'id_user_asignacion' => $validatedData['id_user_asignacion'] ?? null,
                'cedula_quien_llama' => $validatedData['cedula_quien_llama'] ?? null,
                'nombre_quien_llama' => $validatedData['nombre_quien_llama'] ?? null,
                'celular_quien_llama' => $validatedData['celular_quien_llama'] ?? null,
                'parentesco_quien_llama' => $validatedData['parentesco_quien_llama'] ?? null,
            ]);

            // Lógica para subir nuevo archivo si lo hay
            $path = null;
            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $safeName = time().'_'.str_replace(' ', '_', $file->getClientOriginalName());
                $folderPath = 'corpentunida/daytrack/'.$interaction->id;
                $path = Storage::disk('s3')->putFileAs($folderPath, $file, $safeName);
            }

            // 2. Crear nueva línea de evolución en Seguimiento (TABLA 2)
            // Siempre que se edita, se crea un nuevo seguimiento para dejar el historial
            if ($request->filled('next_action_type') || $path !== null || $request->filled('interaction_url') || $interaction->wasChanged('outcome')) {
                $interaction->seguimientos()->create([
                    'agent_id' => Auth::id(), // Quien hizo la actualización
                    'id_user_asignacion' => $validatedData['id_user_asignacion'] ?? Auth::id(),
                    'outcome' => $validatedData['outcome'],
                    'next_action_type' => $request->input('next_action_type') ?? 1,
                    'next_action_date' => $request->input('next_action_date'),
                    'next_action_notes' => $request->input('next_action_notes'),
                    'interaction_url' => $request->input('interaction_url'),
                    'attachment_urls' => $path,
                ]);
            }

            return redirect()->route('interactions.index')->with('success', 'Interacción actualizada exitosamente.');
        });
    }

    public function destroy(Interaction $interaction)
    {
        try {
            return DB::transaction(function () use ($interaction) {
                // Recorremos los seguimientos para borrar todos los archivos en S3
                foreach ($interaction->seguimientos as $seguimiento) {
                    if (! empty($seguimiento->attachment_urls)) {
                        // attachment_urls se guarda como una sola ruta (string) en store()/update()
                        // (líneas 803/913), nunca como array real — el cast 'array' del modelo hace
                        // mal el round-trip con un string simple (json_decode de un string JSON
                        // devuelve el string, no un array), así que un foreach directo lo saltaba
                        // sin borrar el archivo en S3. (array) normaliza ambos casos.
                        foreach ((array) $seguimiento->attachment_urls as $ruta) {
                            Storage::disk('s3')->delete($ruta);
                        }
                    }
                    $seguimiento->delete();
                }

                $interaction->delete();

                return redirect()->route('interactions.index')->with('success', 'Interacción eliminada exitosamente.');
            });
        } catch (Exception $e) {
            Log::error('Error al eliminar interacción '.$interaction->id.': '.$e->getMessage());

            return redirect()->back()->with('error', 'Hubo un error al eliminar la interacción.');
        }
    }

    public function downloadAttachment($fileName)
    {
        try {
            // Ajustar ruta según corresponda si pasaste un nombre de archivo o ruta completa
            $path = 'corpentunida/daytrack/'.$fileName;

            if (! Storage::disk('s3')->exists($path)) {
                abort(404, 'Archivo no encontrado.');
            }

            return Storage::disk('s3')->download($path);
        } catch (Exception $e) {
            Log::error('Error al descargar archivo: '.$e->getMessage());
            abort(404, 'Archivo no encontrado.');
        }
    }

    public function viewAttachment($fileName)
    {
        try {
            $path = 'corpentunida/daytrack/'.$fileName;

            if (! Storage::disk('s3')->exists($path)) {
                abort(404, 'Archivo no encontrado.');
            }

            $file = Storage::disk('s3')->get($path);
            $mimeType = Storage::disk('s3')->mimeType($path);

            return response($file)->header('Content-Type', $mimeType);
        } catch (Exception $e) {
            Log::error('Error al visualizar archivo: '.$e->getMessage());
            abort(404, "Archivo no encontrado: {$fileName}");
        }
    }

    /**
     * Obtener datos del cliente para AJAX
     */
    public function getCliente($cod_ter)
    {
        try {
            $cliente = MaeTerceros::select(['cod_ter', 'nom_ter', 'nom1', 'apl1', 'email', 'dir', 'tel1', 'cel', 'ciudad', 'dpto', 'pais', 'cod_dist', 'barrio', 'cod_est', 'congrega'])
                ->where('cod_ter', $cod_ter)
                ->with(['maeTipos:id,nombre', 'distrito:COD_DIST,NOM_DIST,DETALLE,COMPUEST', 'congregacion:codigo,nombre'])
                ->first();

            if (! $cliente) {
                return response()->json(['error' => 'Cliente no encontrado'], 404);
            }

            // AQUI TAMBIEN SE AJUSTÓ LA RELACIÓN CON SEGUIMIENTOS
            $history = Interaction::with(['agent', 'channel', 'type', 'outcomeRelation', 'usuarioAsignado', 'seguimientos'])
                ->where('client_id', $cod_ter)
                ->orderByDesc('interaction_date')
                ->limit(10)
                ->get()
                ->map(function ($item) {
                    // Extraemos el último seguimiento activo para esta interacción
                    $ultimoSeg = $item->seguimientos->sortByDesc('created_at')->first();

                    return [
                        'id' => $item->id,
                        'client_id' => $item->client_id,
                        'agent' => $item->agent ? $item->agent->name : 'No asignado',
                        'date' => $item->interaction_date ? $item->interaction_date->format('d/m/Y H:i') : null,
                        'date_iso' => $item->interaction_date,
                        'duration' => $item->duration ?? 0,
                        'type' => $item->type ? $item->type->name : 'No definido',
                        'channel' => $item->channel ? $item->channel->name : 'No definido',
                        'outcome' => $item->outcomeRelation ? $item->outcomeRelation->name : 'No definido',
                        'notes' => $item->notes,
                        'parent_interaction_id' => $item->parent_interaction_id,

                        // DATOS EXTRAIDOS DEL SEGUIMIENTO
                        'next_action_date' => $ultimoSeg && $ultimoSeg->next_action_date ? $ultimoSeg->next_action_date->format('d/m/Y H:i') : null,
                        'next_action_type' => $ultimoSeg->next_action_type ?? null,
                        'next_action_notes' => $ultimoSeg->next_action_notes ?? null,
                        'attachment_urls' => $ultimoSeg->attachment_urls ?? [],
                        'interaction_url' => $ultimoSeg->interaction_url ?? null,

                        'parentesco_quien_llama' => $item->parentesco_quien_llama,
                        'cedula_quien_llama' => $item->cedula_quien_llama,
                        'nombre_quien_llama' => $item->nombre_quien_llama,
                        'celular_quien_llama' => $item->celular_quien_llama,

                        'id_linea_de_obligacion' => $item->id_linea_de_obligacion,
                        'linea_obligacion_name' => $item->lineaDeObligacion ?? null,

                        'id_user_asignacion' => $item->id_user_asignacion,
                        'usuario_asignado_name' => $item->usuarioAsignado ? $item->usuarioAsignado->name : null,
                    ];
                });

            $response = [
                'cod_ter' => $cliente->cod_ter,
                'nom_ter' => $cliente->nom_ter ?? 'No registrado',
                'nom1' => $cliente->nom1,
                'apl1' => $cliente->apl1,
                'email' => $cliente->email ?? 'No registrado',
                'dir' => $cliente->dir ?? 'No registrado',
                'tel1' => $cliente->tel1 ?? 'No registrado',
                'cel' => $cliente->cel ?? 'No registrado',
                'ciudad' => $cliente->ciudad ?? 'No registrado',
                'dpto' => $cliente->dpto ?? 'No registrado',
                'pais' => $cliente->pais ?? 'No registrado',
                'cod_dist' => $cliente->cod_dist ?? 'No registrado',
                'barrio' => $cliente->barrio ?? 'No registrado',
                'cod_est' => $cliente->cod_est ?? 'No registrado',
                'congrega' => $cliente->congrega ?? 'No registrado',

                'history' => $history,

                'maeTipos' => $cliente->maeTipos
                    ? [
                        'id' => $cliente->maeTipos->id,
                        'nombre' => $cliente->maeTipos->nombre ?? 'No definido',
                    ]
                    : null,

                'distrito' => $cliente->distrito
                    ? [
                        'COD_DIST' => $cliente->distrito->COD_DIST,
                        'NOM_DIST' => $cliente->distrito->NOM_DIST ?? 'No definido',
                        'DETALLE' => $cliente->distrito->DETALLE ?? 'No definido',
                        'COMPUEST' => $cliente->distrito->COMPUEST ?? 'No definido',
                    ]
                    : null,

                'congregaciones' => $cliente->congregacion
                    ? [
                        'codigo' => $cliente->congregacion->codigo,
                        'nombre' => $cliente->congregacion->nombre ?? 'No definido',
                    ]
                    : null,
            ];

            return response()->json($response);
        } catch (Exception $e) {
            \Log::error('Error al cargar cliente '.$cod_ter.': '.$e->getMessage());

            return response()->json(['error' => 'Error interno del servidor: '.$e->getMessage()], 500);
        }
    }

    /**
     * Buscar clientes para Select2 AJAX
     */
    public function searchClients(Request $request)
    {
        $search = trim((string) $request->get('q', ''));

        if ($search === '') {
            return response()->json(['results' => [], 'pagination' => ['more' => false]]);
        }

        // Antes: LIKE '%texto%' por 6 columnas — sin índice posible por el comodín al inicio,
        // 220-700ms por letra (medido). Mismo patrón que ya usan EmpleadoController::buscarTercero()
        // y ComaeExCliController: FULLTEXT (índice ft_mae_terceros_nombres) para los nombres, LIKE
        // solo para cod_ter (numérico, FULLTEXT no aplica ahí). Medido: ~150-200ms y estable.
        // Solo letras y números por palabra: cualquier símbolo (%, comillas, paréntesis...) es
        // sintaxis reservada de BOOLEAN MODE y un término que quede vacío o solo con símbolos
        // ("%" suelto, por ejemplo) hace fallar el MATCH con un error de sintaxis SQL.
        $terminoBooleano = collect(preg_split('/\s+/', $search))
            ->map(fn ($palabra) => preg_replace('/[^\p{L}\p{N}]/u', '', $palabra))
            ->filter(fn ($palabra) => $palabra !== '')
            ->map(fn ($palabra) => '+'.$palabra.'*')
            ->implode(' ');

        $query = MaeTerceros::select('cod_ter', 'nom_ter', 'apl1', 'apl2', 'nom1', 'nom2', 'cod_dist', 'congrega')
            ->where('cod_ter', 'like', "%{$search}%");

        if ($terminoBooleano !== '') {
            $query->orWhereRaw(
                'MATCH(nom1, nom2, apl1, apl2, nom_ter) AGAINST(? IN BOOLEAN MODE)',
                [$terminoBooleano]
            );
        }

        $clientes = $query->orderBy('nom_ter')->paginate(50);

        return response()->json([
            'results' => $clientes->items(),
            'pagination' => [
                'more' => $clientes->hasMorePages(),
            ],
        ]);
    }

    /**
     * Buscar usuarios para Select2 AJAX (Delegar a otro)
     */
    public function searchUsers(Request $request)
    {
        $search = $request->get('q');

        $users = User::select('id', 'name', 'email')
            ->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->paginate(50);

        return response()->json([
            'results' => $users->map(function ($user) {
                return [
                    'id' => $user->id,
                    'text' => $user->name.' ('.$user->email.')',
                ];
            }),
            'pagination' => [
                'more' => $users->hasMorePages(),
            ],
        ]);
    }

    /**
     * Obtiene los seguimientos de una interacción vía AJAX para el modal.
     */
    public function getSeguimientos(Interaction $interaction)
    {
        // Cargamos los seguimientos con sus relaciones para que el modal tenga toda la info
        $seguimientos = $interaction->seguimientos()
            ->with(['outcomeRelation', 'creator', 'assignedUser', 'nextAction'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($seg) {
                return [
                    'resultado' => $seg->outcomeRelation->name ?? 'N/A',
                    'fecha_creacion' => $seg->created_at->format('d/m/Y H:i'),
                    'notas' => $seg->next_action_notes ?? 'Sin notas',
                    'agente' => $seg->creator->name ?? 'Sistema',
                    'accion' => $seg->nextAction->name ?? 'N/A',
                    'fecha_accion' => optional($seg->next_action_date)->format('d/m/Y') ?? 'N/A',
                ];
            });

        return response()->json($seguimientos);
    }
}
