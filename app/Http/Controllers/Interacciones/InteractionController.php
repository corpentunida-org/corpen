<?php

namespace App\Http\Controllers\Interacciones;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;

use App\Models\Maestras\maeTerceros;
use App\Models\Maestras\maeDistritos;
use App\Models\User;
use App\Models\Interacciones\Interaction;
use App\Models\Interacciones\IntSeguimiento;
use App\Models\Interacciones\IntChannel;
use App\Models\Interacciones\IntType;
use App\Models\Interacciones\IntOutcome;
use App\Models\Interacciones\IntNextAction;

// Importar los modelos para los catálogos
use App\Models\Archivo\GdoArea;
use App\Models\Archivo\GdoCargo;
use App\Models\Creditos\LineaCredito;

class InteractionController extends Controller
{
    public function report(Request $request)
    {
        // 1. Definir rango de fechas y filtros adicionales
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate   = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());
        
        $filtroDistrito = $request->input('distrito_id');
        $filtroLinea    = $request->input('linea_id');
        
        // NUEVO: Capturar los inputs de Agente y Cliente
        $filtroAgente   = $request->input('agent_id');
        $filtroCliente  = $request->input('client_id');

        $start = Carbon::parse($startDate)->startOfDay();
        $end   = Carbon::parse($endDate)->endOfDay();

        // 2. Query Base para interacciones dentro del rango
        $baseQuery = Interaction::whereBetween('interaction_date', [$start, $end]);

        // Aplicar Filtro de Distrito (A través del cliente) si existe
        if ($filtroDistrito) {
            $baseQuery->whereHas('client', function($q) use ($filtroDistrito) {
                $q->where('cod_dist', $filtroDistrito);
            });
        }

        // Aplicar Filtro de Línea de Crédito si existe
        if ($filtroLinea) {
            $baseQuery->where('id_linea_de_obligacion', $filtroLinea);
        }

        // NUEVO: Aplicar Filtro de Agente si existe
        if ($filtroAgente) {
            $baseQuery->where('agent_id', $filtroAgente);
        }

        // NUEVO: Aplicar Filtro de Cliente si existe
        if ($filtroCliente) {
            $baseQuery->where('client_id', $filtroCliente);
        }

        // 3. Cálculos de Tarjetas (KPIs)
        $totalInteracciones = (clone $baseQuery)->count();

        $exitosas = (clone $baseQuery)->whereHas('outcomeRelation', function($q) {
            $q->where('estado', 1);
        })->count();

        $pendientes = (clone $baseQuery)->whereHas('outcomeRelation', function($q) {
            $q->where('estado', '!=', 1)->orWhereNull('estado');
        })->count();

        // NUEVO: Se agregaron $filtroAgente y $filtroCliente al use() de la subconsulta
        $vencidas = IntSeguimiento::whereHas('interaction', function($q) use ($start, $end, $filtroDistrito, $filtroLinea, $filtroAgente, $filtroCliente) {
                $q->whereBetween('interaction_date', [$start, $end])
                  ->whereHas('outcomeRelation', function($q2) {
                      $q2->where('estado', '!=', 1)->orWhereNull('estado');
                  });
                
                // Replicar filtros en seguimientos
                if ($filtroDistrito) {
                    $q->whereHas('client', function($q3) use ($filtroDistrito) {
                        $q3->where('cod_dist', $filtroDistrito);
                    });
                }
                if ($filtroLinea) {
                    $q->where('id_linea_de_obligacion', $filtroLinea);
                }
                
                // NUEVO: Replicar filtros de Agente y Cliente en seguimientos
                if ($filtroAgente) {
                    $q->where('agent_id', $filtroAgente);
                }
                if ($filtroCliente) {
                    $q->where('client_id', $filtroCliente);
                }
            })
            ->whereNotNull('next_action_date')
            ->where('next_action_date', '<', Carbon::now())
            ->count();

        $stats = [
            'total'      => $totalInteracciones,
            'successful' => $exitosas,
            'pending'    => $pendientes,
            'overdue'    => $vencidas,
        ];

        // 4. Datos para Gráficos
        
        // a. Agrupación por Canal
        $canalesData = (clone $baseQuery)
            ->select('interaction_channel', DB::raw('count(*) as total'))
            ->with('channel') 
            ->groupBy('interaction_channel')
            ->get();

        $chartCanales = [
            'labels' => $canalesData->map(fn($item) => $item->channel->name ?? 'Desconocido')->toArray(),
            'data'   => $canalesData->pluck('total')->toArray(),
        ];

        // b. Agrupación por Resultado (Outcome)
        $resultadosData = (clone $baseQuery)
            ->select('outcome', DB::raw('count(*) as total'))
            ->with('outcomeRelation')
            ->groupBy('outcome')
            ->get();

        $chartResultados = [
            'labels' => $resultadosData->map(fn($item) => $item->outcomeRelation->name ?? 'Sin Estado')->toArray(),
            'data'   => $resultadosData->pluck('total')->toArray(),
        ];

        // c. Top 5 Agentes con más interacciones
        $agentesData = (clone $baseQuery)
            ->select('agent_id', DB::raw('count(*) as total'))
            ->with('agent') 
            ->groupBy('agent_id')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $chartAgentes = [
            'labels' => $agentesData->map(fn($item) => $item->agent->name ?? 'Sin Agente')->toArray(),
            'data'   => $agentesData->pluck('total')->toArray(),
        ];

        // d. Top 5 Clientes
        $clientesData = (clone $baseQuery)
            ->select('client_id', DB::raw('count(*) as total'))
            ->with('client') 
            ->groupBy('client_id')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $chartClientes = [
            'labels' => $clientesData->map(fn($item) => $item->client->nom_ter ?? 'Cliente '.$item->client_id)->toArray(),
            'data'   => $clientesData->pluck('total')->toArray(),
        ];

        // e. Agrupación por Línea de Crédito
        $lineasData = (clone $baseQuery)
            ->select('id_linea_de_obligacion', DB::raw('count(*) as total'))
            ->with('lineaDeObligacion') 
            ->groupBy('id_linea_de_obligacion')
            ->orderByDesc('total')
            ->limit(5) // Top 5 para el gráfico
            ->get();

        $chartLineas = [
            'labels' => $lineasData->map(fn($item) => optional($item->lineaDeObligacion)->nombre ?? 'Sin Línea')->toArray(), 
            'data'   => $lineasData->pluck('total')->toArray(),
        ];

        // f. Agrupación por Distrito (Relación anidada)
        $distritosInteracciones = (clone $baseQuery)
            ->with(['client.distrito'])
            ->get();

        $distritosAgrupados = $distritosInteracciones->groupBy(function ($item) {
            // CORRECCIÓN AQUÍ: Usamos NOM_DIST según tu modelo maeDistritos
            return optional(optional($item->client)->distrito)->NOM_DIST ?? 'Sin Distrito';
        })->map(function ($row) {
            return $row->count();
        })->sortByDesc(function ($count) {
            return $count;
        })->take(5);

        $chartDistritos = [
            'labels' => $distritosAgrupados->keys()->toArray(),
            'data'   => $distritosAgrupados->values()->toArray(),
        ];

        // g. (NUEVO) Top 5 Agentes por Seguimientos
        $seguimientosAgentesData = IntSeguimiento::whereHas('interaction', function($q) use ($start, $end, $filtroDistrito, $filtroLinea, $filtroAgente, $filtroCliente) {
                $q->whereBetween('interaction_date', [$start, $end]);
                if ($filtroDistrito) {
                    $q->whereHas('client', function($q3) use ($filtroDistrito) {
                        $q3->where('cod_dist', $filtroDistrito);
                    });
                }
                if ($filtroLinea) {
                    $q->where('id_linea_de_obligacion', $filtroLinea);
                }
                if ($filtroAgente) {
                    $q->where('agent_id', $filtroAgente);
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
            'labels' => $seguimientosAgentesData->map(fn($item) => optional($item->creator)->name ?? 'Sin Agente')->toArray(),
            'data'   => $seguimientosAgentesData->pluck('total')->toArray(),
        ];

        // 5. Listas para los select de Filtro
        // Asegúrate de tener el modelo maeDistritos importado arriba
        $listDistritos = maeDistritos::all(); 
        $listLineas    = LineaCredito::all();
        
        // NUEVO: Consultas para los selects de Agentes y Clientes
        $listAgentes   = User::select('id', 'name')->get(); 
        // Límite de 1000 agregado por seguridad de rendimiento si tu base de clientes es muy grande.
        $listClientes  = maeTerceros::select('cod_ter', 'nom_ter')->limit(1000)->get(); 

        // 6. Retornar Vista 
        return view('interactions.reportes.report', compact(
            'stats', 
            'chartCanales', 
            'chartResultados',
            'chartAgentes',
            'chartClientes',
            'chartLineas',
            'chartDistritos',
            'chartSeguimientosAgentes', // <-- NUEVO GRÁFICO AGREGADO AQUÍ
            'startDate',
            'endDate',
            'filtroDistrito',
            'filtroLinea',
            'filtroAgente',  // NUEVO
            'filtroCliente', // NUEVO
            'listDistritos',
            'listLineas',
            'listAgentes',   // NUEVO
            'listClientes'   // NUEVO
        ));
    }
    public function reportPdf(Request $request)
    {
        // 1. Definir rango de fechas y filtros adicionales (Igual que en report)
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate   = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());
        
        $filtroDistrito = $request->input('distrito_id');
        $filtroLinea    = $request->input('linea_id');
        $filtroAgente   = $request->input('agent_id');
        $filtroCliente  = $request->input('client_id');

        $start = Carbon::parse($startDate)->startOfDay();
        $end   = Carbon::parse($endDate)->endOfDay();

        // 2. Query Base para interacciones dentro del rango
        $baseQuery = Interaction::whereBetween('interaction_date', [$start, $end]);

        if ($filtroDistrito) {
            $baseQuery->whereHas('client', function($q) use ($filtroDistrito) {
                $q->where('cod_dist', $filtroDistrito);
            });
        }
        if ($filtroLinea) {
            $baseQuery->where('id_linea_de_obligacion', $filtroLinea);
        }
        if ($filtroAgente) {
            $baseQuery->where('agent_id', $filtroAgente);
        }
        if ($filtroCliente) {
            $baseQuery->where('client_id', $filtroCliente);
        }

        // 3. Cálculos de Tarjetas (KPIs)
        $totalInteracciones = (clone $baseQuery)->count();

        $exitosas = (clone $baseQuery)->whereHas('outcomeRelation', function($q) {
            $q->where('estado', 1);
        })->count();

        $pendientes = (clone $baseQuery)->whereHas('outcomeRelation', function($q) {
            $q->where('estado', '!=', 1)->orWhereNull('estado');
        })->count();

        $vencidas = IntSeguimiento::whereHas('interaction', function($q) use ($start, $end, $filtroDistrito, $filtroLinea, $filtroAgente, $filtroCliente) {
                $q->whereBetween('interaction_date', [$start, $end])
                  ->whereHas('outcomeRelation', function($q2) {
                      $q2->where('estado', '!=', 1)->orWhereNull('estado');
                  });
                
                if ($filtroDistrito) {
                    $q->whereHas('client', function($q3) use ($filtroDistrito) {
                        $q3->where('cod_dist', $filtroDistrito);
                    });
                }
                if ($filtroLinea) {
                    $q->where('id_linea_de_obligacion', $filtroLinea);
                }
                if ($filtroAgente) {
                    $q->where('agent_id', $filtroAgente);
                }
                if ($filtroCliente) {
                    $q->where('client_id', $filtroCliente);
                }
            })
            ->whereNotNull('next_action_date')
            ->where('next_action_date', '<', Carbon::now())
            ->count();

        $stats = [
            'total'      => $totalInteracciones,
            'successful' => $exitosas,
            'pending'    => $pendientes,
            'overdue'    => $vencidas,
        ];

        // 4. Datos para Tablas del PDF
        
        $canalesData = (clone $baseQuery)->select('interaction_channel', DB::raw('count(*) as total'))->with('channel')->groupBy('interaction_channel')->get();
        $chartCanales = [
            'labels' => $canalesData->map(fn($item) => $item->channel->name ?? 'Desconocido')->toArray(),
            'data'   => $canalesData->pluck('total')->toArray(),
        ];

        $resultadosData = (clone $baseQuery)->select('outcome', DB::raw('count(*) as total'))->with('outcomeRelation')->groupBy('outcome')->get();
        $chartResultados = [
            'labels' => $resultadosData->map(fn($item) => $item->outcomeRelation->name ?? 'Sin Estado')->toArray(),
            'data'   => $resultadosData->pluck('total')->toArray(),
        ];

        $agentesData = (clone $baseQuery)->select('agent_id', DB::raw('count(*) as total'))->with('agent')->groupBy('agent_id')->orderByDesc('total')->limit(5)->get();
        $chartAgentes = [
            'labels' => $agentesData->map(fn($item) => $item->agent->name ?? 'Sin Agente')->toArray(),
            'data'   => $agentesData->pluck('total')->toArray(),
        ];

        $clientesData = (clone $baseQuery)->select('client_id', DB::raw('count(*) as total'))->with('client')->groupBy('client_id')->orderByDesc('total')->limit(5)->get();
        $chartClientes = [
            'labels' => $clientesData->map(fn($item) => $item->client->nom_ter ?? 'Cliente '.$item->client_id)->toArray(),
            'data'   => $clientesData->pluck('total')->toArray(),
        ];

        $lineasData = (clone $baseQuery)->select('id_linea_de_obligacion', DB::raw('count(*) as total'))->with('lineaDeObligacion')->groupBy('id_linea_de_obligacion')->orderByDesc('total')->limit(5)->get();
        $chartLineas = [
            'labels' => $lineasData->map(fn($item) => optional($item->lineaDeObligacion)->nombre ?? 'Sin Línea')->toArray(), 
            'data'   => $lineasData->pluck('total')->toArray(),
        ];

        $distritosInteracciones = (clone $baseQuery)->with(['client.distrito'])->get();
        $distritosAgrupados = $distritosInteracciones->groupBy(function ($item) {
            return optional(optional($item->client)->distrito)->NOM_DIST ?? 'Sin Distrito';
        })->map(function ($row) { return $row->count(); })->sortByDesc(function ($count) { return $count; })->take(5);
        $chartDistritos = [
            'labels' => $distritosAgrupados->keys()->toArray(),
            'data'   => $distritosAgrupados->values()->toArray(),
        ];

        $seguimientosAgentesData = IntSeguimiento::whereHas('interaction', function($q) use ($start, $end, $filtroDistrito, $filtroLinea, $filtroAgente, $filtroCliente) {
                $q->whereBetween('interaction_date', [$start, $end]);
                if ($filtroDistrito) $q->whereHas('client', function($q3) use ($filtroDistrito) { $q3->where('cod_dist', $filtroDistrito); });
                if ($filtroLinea) $q->where('id_linea_de_obligacion', $filtroLinea);
                if ($filtroAgente) $q->where('agent_id', $filtroAgente);
                if ($filtroCliente) $q->where('client_id', $filtroCliente);
            })->select('agent_id', DB::raw('count(*) as total'))->with('creator')->groupBy('agent_id')->orderByDesc('total')->limit(5)->get();
        $chartSeguimientosAgentes = [
            'labels' => $seguimientosAgentesData->map(fn($item) => optional($item->creator)->name ?? 'Sin Agente')->toArray(),
            'data'   => $seguimientosAgentesData->pluck('total')->toArray(),
        ];

        // 5. Generar PDF
        // Usamos el facade de Barryvdh\DomPDF
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('interactions.reportes.pdf', compact(
            'stats', 
            'chartCanales', 
            'chartResultados',
            'chartAgentes',
            'chartClientes',
            'chartLineas',
            'chartDistritos',
            'chartSeguimientosAgentes',
            'startDate',
            'endDate'
        ));

        // Formato vertical A4
        $pdf->setPaper('A4', 'portrait');

        // Retorna el PDF en el navegador para visualizar/imprimir
        return $pdf->stream('informe_interacciones_' . Carbon::now()->format('Ymd_Hi') . '.pdf');
    }
    /**
     * Muestra la lista de interacciones con filtros y búsqueda.
     */
    public function index(Request $request)
    {
        // --- 1. CONSTRUIMOS LA CONSULTA BASE ---
        $baseQuery = Interaction::with(['client', 'agent.cargoRelation.gdoArea', 'channel', 'type', 'outcomeRelation', 'lineaDeObligacion', 'usuarioAsignado', 'seguimientos']);

        // --- 2. APLICAMOS LOS FILTROS (Solo Búsqueda y Rango de Fechas) ---

        // A. Filtro de Búsqueda General
        if ($request->filled('search')) {
            $search = $request->input('search');
            $baseQuery->where(function ($q) use ($search) {
                // 1. Busca en campos directos de la tabla interacciones
                $q->where('id', 'LIKE', "%{$search}%")
                    ->orWhere('notes', 'LIKE', "%{$search}%")
                    ->orWhere('cedula_quien_llama', 'LIKE', "%{$search}%")
                    ->orWhere('nombre_quien_llama', 'LIKE', "%{$search}%")
                    ->orWhere('celular_quien_llama', 'LIKE', "%{$search}%")
                    ->orWhere('parentesco_quien_llama', 'LIKE', "%{$search}%")
                    ->orWhere('id_linea_de_obligacion', 'LIKE', "%{$search}%")
                    
                    // 2. Busca en Cliente y su Distrito
                    ->orWhereHas('client', function ($query) use ($search) {
                        $query->where('nom_ter', 'LIKE', "%{$search}%")
                              ->orWhere('cod_ter', 'LIKE', "%{$search}%")
                              // Relación anidada: Cliente -> Distrito
                              ->orWhereHas('distrito', function ($qDistrito) use ($search) {
                                  $qDistrito->where('NOM_DIST', 'LIKE', "%{$search}%");
                              });
                    })
                    
                    // 3. Busca en Agente y su Cargo/Área
                    ->orWhereHas('agent', function ($query) use ($search) {
                        $query->where('name', 'LIKE', "%{$search}%")
                              ->orWhereHas('cargoRelation', function($qCargo) use ($search) {
                                  $qCargo->where('nombre_cargo', 'LIKE', "%{$search}%")
                                         ->orWhereHas('gdoArea', function($qArea) use ($search) {
                                             $qArea->where('nombre', 'LIKE', "%{$search}%");
                                         });
                              });
                    })

                    // 4. Busca por Canal
                    ->orWhereHas('channel', function ($query) use ($search) {
                        $query->where('name', 'LIKE', "%{$search}%");
                    })

                    // 5. Busca por Resultado (Outcome)
                    ->orWhereHas('outcomeRelation', function ($query) use ($search) {
                        $query->where('name', 'LIKE', "%{$search}%");
                    })

                    // 6. Busca por Línea de Obligación
                    ->orWhereHas('lineaDeObligacion', function ($query) use ($search) {
                        $query->where('nombre', 'LIKE', "%{$search}%");
                    })

                    // 7. Busca por el Usuario al que fue Asignado
                    ->orWhereHas('usuarioAsignado', function ($query) use ($search) {
                        $query->where('name', 'LIKE', "%{$search}%");
                    });
            });
        }

        // B. Filtro de Rango de Fechas (Interacción)
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $baseQuery->whereBetween('interaction_date', [$request->input('start_date') . ' 00:00:00', $request->input('end_date') . ' 23:59:59']);
        } elseif ($request->filled('start_date')) {
            $baseQuery->where('interaction_date', '>=', $request->input('start_date') . ' 00:00:00');
        } elseif ($request->filled('end_date')) {
            $baseQuery->where('interaction_date', '<=', $request->input('end_date') . ' 23:59:59');
        }

        // --- 3. OBTENEMOS LOS DATOS PARA LAS ESTADÍSTICAS Y PESTAÑAS (Con filtros aplicados) ---
        $countQuery = clone $baseQuery;
        if (!auth()->user()->hasPermission('interacciones.listado.todos')) {
            $countQuery->where(function ($q) {
                $q->where('agent_id', Auth::id())->orWhere('id_user_asignacion', Auth::id());
            });
            $baseQuery->where(function ($q) {
                $q->where('agent_id', Auth::id())->orWhere('id_user_asignacion', Auth::id());
            });
        }
        $stats = [
            'total' => $countQuery->count(),
            'successful' => (clone $countQuery)->whereHas('outcomeRelation', fn($q) => $q->where('estado', 1))->count(),
            'pending' => (clone $countQuery)->whereHas('outcomeRelation', fn($q) => $q->where('estado', 0))->count(),
            'today' => (clone $countQuery)
                ->where(function ($q) {
                    $q->whereDate('interaction_date', today())->orWhereDate('updated_at', today());
                })
                ->count(),
            'overdue' => (clone $countQuery)
                ->whereHas('outcomeRelation', fn($q) => $q->where('estado', 0))
                ->whereHas('seguimientos', fn($q) => $q->where('next_action_date', '<', today()->startOfDay()))
                ->count(),
        ];

        $collectionsForTabs = [
            'successful' => (clone $baseQuery)
                ->whereHas('outcomeRelation', function ($q) {
                    $q->where('estado', 1);
                })
                ->get(),

            'pending' => (clone $baseQuery)
                ->whereHas('outcomeRelation', function ($q) {
                    $q->where('estado', 0);
                })
                ->get(),

            'today' => (clone $baseQuery)
                ->where(function ($q) {
                    $q->whereDate('interaction_date', today())->orWhereDate('updated_at', today());
                })
                ->get(),

            'overdue' => (clone $baseQuery)
                ->whereHas('outcomeRelation', function ($q) {
                    $q->where('estado', 0);
                })
                ->whereHas('seguimientos', function ($q) {
                    $q->where('next_action_date', '<', today()->startOfDay());
                })
                ->get(),
        ];

        // --- 4. OBTENEMOS LA COLECCIÓN PAGINADA PARA LA PESTAÑA "TODOS" ---
        $interactions = $baseQuery->orderBy('id', 'desc')->paginate(100);

        // appends() asegura que al cambiar de página en la paginación, no se pierdan los filtros
        $interactions->appends($request->query());

        // --- 5. DATOS PARA LOS SELECT DE LA VISTA ---
        $channels = IntChannel::orderBy('name')->pluck('name', 'id');
        $types = IntType::orderBy('name')->pluck('name', 'id');
        $outcomes = IntOutcome::orderBy('name')->pluck('name', 'id');
        $areas = GdoArea::orderBy('nombre')->pluck('nombre', 'id');
        $cargos = GdoCargo::orderBy('nombre_cargo')->pluck('nombre_cargo', 'id');
        $lineas = LineaCredito::orderBy('nombre')->pluck('nombre', 'id');

        // --- 6. PASAMOS TODAS LAS VARIABLES A LA VISTA ---
        return view('interactions.index', compact('interactions', 'stats', 'collectionsForTabs', 'channels', 'types', 'outcomes', 'areas', 'cargos', 'lineas'));
    }

    /**
     * Muestra detalles y estadísticas de una interacción.
     */
    public function show(Interaction $interaction)
    {
        // 1. Cargamos todas las relaciones, incluyendo las del timeline de seguimientos
        $interaction->load([
            'agent',
            'client.distrito', // <-- MODIFICACIÓN: Se agregó '.distrito' para cargar la relación
            'channel',
            'type',
            'outcomeRelation',
            'lineaDeObligacion',
            'usuarioAsignado',
            // Cargamos relaciones de seguimientos para el Timeline
            'seguimientos.outcomeRelation',
            'seguimientos.creator',
            'seguimientos.assignedUser',
            'seguimientos.nextAction',
        ]);

        // 2. Lógica del Gráfico (Rendimiento del Agente)
        $agentId = $interaction->agent_id;
        $range = request()->get('range', 'day');
        $query = Interaction::where('agent_id', $agentId);

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
            $clientHistory = Interaction::with(['agent', 'channel', 'type', 'outcomeRelation', 'lineaDeObligacion', 'usuarioAsignado'])
                ->where('client_id', $interaction->client_id)
                ->orderByDesc('interaction_date')
                ->get();
        }

        // 4. DATOS PARA EL MODAL
        $outcomes = IntOutcome::all();
        $nextActions = IntNextAction::all();
        $users = User::orderBy('name')->get();

        return view(
            'interactions.show',
            compact(
                'interaction',
                'labels',
                'totals',
                'range',
                'clientHistory',
                'outcomes',
                'nextActions',
                'users',
            )
        );
    }

    /**
     * Formulario para crear una nueva interacción.
     */
    public function create()
    {
        $interaction = new Interaction();

        $channels = IntChannel::all();
        $types = IntType::all();
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

        return view('interactions.create', compact('interaction', 'channels', 'types', 'outcomes', 'nextActions', 'areas', 'cargos', 'lineasCredito', 'idCargoAgente', 'idAreaAgente'));
    }

    /**
     * Guarda una nueva interacción en la base de datos.
     */
    public function store(Request $request)
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

        return DB::transaction(function () use ($request, $validatedData) {
            $duration = $validatedData['duration'] ?? 0;
            $agentId = Auth::id();

            // 1. Guardar Interaction (TABLA 1)
            $interaction = Interaction::create([
                'client_id' => $validatedData['client_id'],
                'agent_id' => $agentId,
                'interaction_date' => $validatedData['interaction_date'],
                'interaction_channel' => $validatedData['interaction_channel'],
                'interaction_type' => $validatedData['interaction_type'],
                'duration' => $duration,
                'outcome' => $validatedData['outcome'],
                'notes' => $validatedData['notes'] ?? '',
                'parent_interaction_id' => $validatedData['parent_interaction_id'] ?? null,
                'id_linea_de_obligacion' => $validatedData['id_linea_de_obligacion'] ?? null,
                'id_user_asignacion' => $validatedData['id_user_asignacion'] ?? null,
                'cedula_quien_llama' => $validatedData['cedula_quien_llama'] ?? null,
                'nombre_quien_llama' => $validatedData['nombre_quien_llama'] ?? null,
                'celular_quien_llama' => $validatedData['celular_quien_llama'] ?? null,
                'parentesco_quien_llama' => $validatedData['parentesco_quien_llama'] ?? null,
            ]);

            // Lógica para subir archivo (Si el usuario adjuntó uno)
            $path = null;
            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');     
                $safeName = time() . '_' . str_replace(' ', '_', $file->getClientOriginalName());     
                $folderPath = "corpentunida/daytrack/" . $interaction->id;
                $path = Storage::disk('s3')->putFileAs($folderPath, $file, $safeName);
            }
           
                $interaction->seguimientos()->create([
                    'agent_id' => $agentId,
                    'id_user_asignacion' => $validatedData['id_user_asignacion'] ?? null,
                    'outcome' => $validatedData['outcome'],
                    'next_action_type' => $request->input('next_action_type') ?? 1, // Por si acaso enviamos un fallback
                    'next_action_date' => $request->input('next_action_date') ?? now(),
                    'next_action_notes' => $request->input('next_action_notes') ?? $request->input('notes'),
                    'interaction_url' => $request->input('interaction_url'),
                    'attachment_urls' => $path,
                ]);
            

            return redirect()->route('interactions.index')->with('success', 'Interacción creada exitosamente.');
        });
    }

    /**
     * Formulario para editar una interacción existente.
     */
    public function edit(Interaction $interaction)
    {
        $channels = IntChannel::all();
        $types = IntType::all();
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
                $safeName = time() . '_' . str_replace(' ', '_', $file->getClientOriginalName());     
                $folderPath = "corpentunida/daytrack/" . $interaction->id;
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
                    if (!empty($seguimiento->attachment_urls)) {
                        foreach ($seguimiento->attachment_urls as $ruta) {
                            Storage::disk('s3')->delete($ruta);
                        }
                    }
                    $seguimiento->delete();
                }

                $interaction->delete();

                return redirect()->route('interactions.index')->with('success', 'Interacción eliminada exitosamente.');
            });
        } catch (Exception $e) {
            Log::error('Error al eliminar interacción ' . $interaction->id . ': ' . $e->getMessage());
            return redirect()->back()->with('error', 'Hubo un error al eliminar la interacción.');
        }
    }

    public function downloadAttachment($fileName)
    {
        try {
            // Ajustar ruta según corresponda si pasaste un nombre de archivo o ruta completa
            $path = 'corpentunida/daytrack/' . $fileName;

            if (!Storage::disk('s3')->exists($path)) {
                abort(404, 'Archivo no encontrado.');
            }

            return Storage::disk('s3')->download($path);
        } catch (Exception $e) {
            Log::error('Error al descargar archivo: ' . $e->getMessage());
            abort(404, 'Archivo no encontrado.');
        }
    }

    public function viewAttachment($fileName)
    {
        try {
            $path = 'corpentunida/daytrack/' . $fileName;

            if (!Storage::disk('s3')->exists($path)) {
                abort(404, 'Archivo no encontrado.');
            }

            $file = Storage::disk('s3')->get($path);
            $mimeType = Storage::disk('s3')->mimeType($path);

            return response($file)->header('Content-Type', $mimeType);
        } catch (Exception $e) {
            Log::error('Error al visualizar archivo: ' . $e->getMessage());
            abort(404, "Archivo no encontrado: {$fileName}");
        }
    }

    /**
     * Obtener datos del cliente para AJAX
     */
    public function getCliente($cod_ter)
    {
        try {
            $cliente = maeTerceros::where('cod_ter', $cod_ter)
                ->with(['maeTipos', 'distrito', 'congregaciones'])
                ->first();

            if (!$cliente) {
                return response()->json(['error' => 'Cliente no encontrado'], 404);
            }

            // AQUI TAMBIEN SE AJUSTÓ LA RELACIÓN CON SEGUIMIENTOS
            $history = Interaction::with(['agent', 'channel', 'type', 'outcomeRelation', 'lineaDeObligacion', 'usuarioAsignado', 'seguimientos'])
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
                        'next_action_date' => ($ultimoSeg && $ultimoSeg->next_action_date) ? $ultimoSeg->next_action_date->format('d/m/Y H:i') : null,
                        'next_action_type' => $ultimoSeg->next_action_type ?? null,
                        'next_action_notes' => $ultimoSeg->next_action_notes ?? null,
                        'attachment_urls' => $ultimoSeg->attachment_urls ?? [],
                        'interaction_url' => $ultimoSeg->interaction_url ?? null,

                        'parentesco_quien_llama' => $item->parentesco_quien_llama,
                        'cedula_quien_llama' => $item->cedula_quien_llama,
                        'nombre_quien_llama' => $item->nombre_quien_llama,
                        'celular_quien_llama' => $item->celular_quien_llama,

                        'id_linea_de_obligacion' => $item->id_linea_de_obligacion,
                        'linea_obligacion_name' => $item->lineaDeObligacion ? $item->lineaDeObligacion->nombre ?? $item->lineaDeObligacion->name : null,

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
                'cel1' => $cliente->cel1 ?? 'No registrado',
                'ciudad' => $cliente->ciudad ?? 'No registrado',
                'departamento' => $cliente->departamento ?? 'No registrado',
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

                'congregaciones' => $cliente->congregaciones
                    ? [
                        'codigo' => $cliente->congregaciones->codigo,
                        'nombre' => $cliente->congregaciones->nombre ?? 'No definido',
                    ]
                    : null,
            ];

            return response()->json($response);
        } catch (\Exception $e) {
            \Log::error('Error al cargar cliente ' . $cod_ter . ': ' . $e->getMessage());
            return response()->json(['error' => 'Error interno del servidor: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Buscar clientes para Select2 AJAX
     */
    public function searchClients(Request $request)
    {
        $search = $request->get('q');

        $clientes = maeTerceros::select('cod_ter', 'nom_ter', 'apl1', 'apl2', 'nom1', 'nom2', 'cod_dist', 'congrega')
            ->where('estado', 1)
            ->where(function ($query) use ($search) {
                $query
                    ->where('nom_ter', 'like', "%{$search}%")
                    ->orWhere('apl1', 'like', "%{$search}%")
                    ->orWhere('apl2', 'like', "%{$search}%")
                    ->orWhere('nom1', 'like', "%{$search}%")
                    ->orWhere('nom2', 'like', "%{$search}%")
                    ->orWhere('cod_ter', 'like', "%{$search}%");
            })
            ->orderBy('nom_ter')
            ->paginate(50);

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
                    'text' => $user->name . ' (' . $user->email . ')',
                ];
            }),
            'pagination' => [
                'more' => $users->hasMorePages(),
            ],
        ]);
    }
}