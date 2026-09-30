<?php

namespace App\Http\Controllers\Certificados;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

// Importación de modelos del módulo de certificados
use App\Models\Certificados\CarSiaPeriodo;
use App\Models\Certificados\CarSiaBloque;
use App\Models\Certificados\CarSiaOperacion;
use App\Models\Certificados\CarSiaOperacionLinea;
use App\Models\Certificados\CarSiaOperacionLog;
use App\Models\Certificados\CarSiaOperacionConfig;
use App\Models\Certificados\CarSiaOperacionAlerta;
use App\Models\Certificados\CarSiaTipoOperacion;

class InformeController extends Controller
{
    public function index(Request $request)
    {
        $bloqueActivo = $request->get('bloque');
        
        if (!$bloqueActivo) {
            $ultimoBloque = CarSiaBloque::select('numero_bloque')->latest('numero_bloque')->first();
            $bloqueActivo = $ultimoBloque ? $ultimoBloque->numero_bloque : 1;
        }

        // Carga ligera del explorador lateral (limitado a lo esencial)
        $periodosAbiertos = CarSiaPeriodo::select('id', 'anio', 'mes')
            ->orderBy('anio', 'desc')
            ->orderBy('mes', 'desc')
            ->take(12)
            ->get()
            ->groupBy('anio');

        $idsPeriodos = $periodosAbiertos->flatten()->pluck('id');
        $bloquesAgrupadosPorPeriodo = CarSiaBloque::select('id', 'numero_bloque', 'id_periodo')
            ->whereIn('id_periodo', $idsPeriodos)
            ->get()
            ->groupBy('id_periodo');

        $textoPeriodo = "Lote de Datos Activo: API-" . str_pad($bloqueActivo, 4, '0', STR_PAD_LEFT);

        // KPI optimizado con consultas directas de conteo SQL (Mucho más rápido)
        $totalLineas = CarSiaOperacionLinea::where('numero_bloque', $bloqueActivo)->count();
        
        $generados = CarSiaOperacionLinea::where('numero_bloque', $bloqueActivo)
            ->whereHas('estadoOperacion', function($q) {
                $q->whereRaw('LOWER(nombre) LIKE ?', ['%generado%'])
                  ->orWhereRaw('LOWER(nombre) LIKE ?', ['%completado%']);
            })->count();

        $kpi = [
            'total_operaciones' => CarSiaOperacion::where('numero_bloque', $bloqueActivo)->count(),
            'total_lineas'      => $totalLineas,
            'generados'         => $generados,
            'pendientes'        => max(0, $totalLineas - $generados), // Cálculo matemático directo evitando subconsultas lentas
        ];

        $buscar = $request->get('buscar');
        $informes = CarSiaBloque::with('periodo')
            ->when($buscar, function($query, $buscar) {
                $query->where('numero_bloque', 'like', "%{$buscar}%")
                      ->orWhere('descripcion', 'like', "%{$buscar}%");
            })
            ->orderBy('numero_bloque', 'desc')
            ->paginate(15)
            ->appends($request->query());

        $historialBloque = CarSiaOperacionLog::with(['usuario:id,name', 'origenEvento', 'eventoAuditoria'])
            ->where('numero_bloque', $bloqueActivo)
            ->latest()
            ->take(15)
            ->get();

        $plantillasAsignadas = CarSiaOperacionConfig::whereHas('operacion', function($q) use ($bloqueActivo) {
                $q->where('numero_bloque', $bloqueActivo);
            })
            ->paginate(10, ['*'], 'plantillas_page');

        $informesProgramados = CarSiaOperacionAlerta::where('numero_bloque', $bloqueActivo)
            ->paginate(10, ['*'], 'programados_page');

        return view('certificados.informes.index', compact(
            'bloqueActivo',
            'textoPeriodo',
            'kpi',
            'informes',
            'periodosAbiertos',
            'bloquesAgrupadosPorPeriodo',
            'historialBloque',
            'plantillasAsignadas',
            'informesProgramados'
        ));
    }

    public function show($numeroBloque)
    {
        $bloqueActivo = $numeroBloque;
        $bloqueInfo = CarSiaBloque::where('numero_bloque', $numeroBloque)->firstOrFail();
        $textoPeriodo = "Lote de Datos: API-" . str_pad($bloqueActivo, 4, '0', STR_PAD_LEFT);

        // 1. Conteo de líneas optimizado
        $totalLineas = CarSiaOperacionLinea::where('numero_bloque', $bloqueActivo)->count();
        
        $generados = CarSiaOperacionLinea::where('numero_bloque', $bloqueActivo)
            ->whereHas('estadoOperacion', function($q) {
                $q->whereRaw('LOWER(nombre) LIKE ?', ['%generado%'])
                  ->orWhereRaw('LOWER(nombre) LIKE ?', ['%completado%']);
            })->count();

        $totalOperaciones = CarSiaOperacion::where('numero_bloque', $bloqueActivo)->count();

        $kpi = [
            'total_operaciones' => $totalOperaciones,
            'total_lineas'      => $totalLineas,
            'generados'         => $generados,
            'pendientes'        => max(0, $totalLineas - $generados),
        ];

        // 2. Gráfico de métodos optimizado con UNA SOLA consulta (groupBy en vez de múltiples count)
        $metodosCounts = CarSiaOperacion::where('numero_bloque', $bloqueActivo)
            ->select('metodo_creacion', \DB::raw('count(*) as total'))
            ->groupBy('metodo_creacion')
            ->pluck('total', 'metodo_creacion')
            ->toArray();

        $chartMetodos = [
            'automaticas' => ($metodosCounts[0] ?? 0) + ($metodosCounts[''] ?? 0),
            'manuales'    => $metodosCounts[1] ?? 0,
        ];

        // 3. Paginación ligera seleccionando solo las columnas necesarias (Evita sobrecarga de memoria)
        $operacionesLote = CarSiaOperacion::with([
                'tercero:id,nom_ter',
                'lineas' => function($q) {
                    $q->select('id', 'id_car_sia_operaciones');
                }
            ])
            ->where('numero_bloque', $bloqueActivo)
            ->select('id', 'numero_bloque', 'id_tercero', 'numero_radicado', 'metodo_creacion')
            ->paginate(15);

        // 4. Limitar registros laterales para que no saturen la vista
        $tiposCertificadosLote = CarSiaTipoOperacion::with(['tipo:id,nombre'])
            ->where('numero_bloque', $bloqueActivo)
            ->take(30)
            ->get();

        $historialBloque = CarSiaOperacionLog::with(['usuario:id,name', 'eventoAuditoria'])
            ->where('numero_bloque', $bloqueActivo)
            ->latest()
            ->take(10)
            ->get();

        return view('certificados.informes.show', compact(
            'bloqueActivo',
            'bloqueInfo',
            'textoPeriodo',
            'kpi',
            'chartMetodos',
            'operacionesLote',
            'tiposCertificadosLote',
            'historialBloque'
        ));
    }

    public function exportarPdf(Request $request)
    {
        $numeroBloque = $request->input('bloque');

        if (!$numeroBloque) {
            return back()->with('error', 'No se ha proporcionado un número de lote válido para exportar.');
        }

        $bloqueInfo = CarSiaBloque::where('numero_bloque', $numeroBloque)->firstOrFail();
        $textoPeriodo = "Lote de Datos: API-" . str_pad($numeroBloque, 4, '0', STR_PAD_LEFT);

        $totalLineas = CarSiaOperacionLinea::where('numero_bloque', $numeroBloque)->count();
        $generados = CarSiaOperacionLinea::where('numero_bloque', $numeroBloque)
            ->whereHas('estadoOperacion', function($q) {
                $q->whereRaw('LOWER(nombre) LIKE ?', ['%generado%'])
                  ->orWhereRaw('LOWER(nombre) LIKE ?', ['%completado%']);
            })->count();

        $kpi = [
            'total_operaciones' => CarSiaOperacion::where('numero_bloque', $numeroBloque)->count(),
            'total_lineas'      => $totalLineas,
            'generados'         => $generados,
            'pendientes'        => max(0, $totalLineas - $generados),
        ];

        // Solo traemos los tipos de certificados (sin tablas gigantes de clientes)
        $tiposCertificadosLote = CarSiaTipoOperacion::with(['tipo:id,nombre'])
            ->where('numero_bloque', $numeroBloque)
            ->take(50)
            ->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('certificados.informes.pdf', compact(
            'numeroBloque',
            'bloqueInfo',
            'textoPeriodo',
            'kpi',
            'tiposCertificadosLote'
        ));

        // Establecer un tamaño de papel y orientación óptima
        return $pdf->setPaper('letter', 'portrait')->download("reporte-estadistico-lote-{$numeroBloque}.pdf");
    }
}