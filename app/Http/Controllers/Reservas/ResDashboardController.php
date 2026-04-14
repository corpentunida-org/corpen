<?php

namespace App\Http\Controllers\Reservas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Reserva\Res_reserva;
use App\Models\Reserva\Res_inmueble;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class ResDashboardController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return ['auth'];
    }

    public function index()
    {
        // Métricas Generales
        $total_inmuebles = Res_inmueble::where('active', 1)->count();
        $reservas_pendientes = Res_reserva::whereIn('res_status_id', [1, 5])->count();
        $reservas_confirmadas = Res_reserva::where('res_status_id', 2)->count();
        
        $ultimas_reservas = Res_reserva::with(['res_inmueble', 'user'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // ---------------------------------------------------------
        // 1. INFORME: Torta por estado de reservas
        // Usamos withTrashed() por si las canceladas tienen SoftDelete
        // ---------------------------------------------------------
        $estadoReservas = Res_reserva::withTrashed()
            ->select('res_status_id', DB::raw('count(*) as total'))
            ->with('res_status') 
            ->groupBy('res_status_id')
            ->get();

        $chartLabels = [];
        $chartData = [];
        // Mapeamos los nombres de los estados y sus totales
        foreach ($estadoReservas as $estado) {
            // Suponiendo que tu modelo Res_status tiene un campo 'name' o 'nombre'. 
            // Ajusta "name" por el campo real de tu tabla de estados si es diferente.
            $nombreEstado = $estado->res_status ? $estado->res_status->name : 'Estado ' . $estado->res_status_id;
            
            // Si el estado es 1 (Solicitada), 2 (Confirmada), 4 (Cancelada), etc.
            if($estado->res_status_id == 1) $nombreEstado = 'Solicitadas';
            if($estado->res_status_id == 2) $nombreEstado = 'Confirmadas';
            if($estado->res_status_id == 4) $nombreEstado = 'Canceladas';
            if($estado->res_status_id == 5) $nombreEstado = 'Con Soporte';

            $chartLabels[] = $nombreEstado;
            $chartData[] = $estado->total;
        }

        // ---------------------------------------------------------
        // 2. INFORME: Día en el que se hicieron más reservas
        // ---------------------------------------------------------
        $diaMasReservas = Res_reserva::withTrashed()
            ->select(DB::raw('DATE(fecha_solicitud) as dia'), DB::raw('count(*) as total'))
            ->groupBy('dia')
            ->orderBy('total', 'desc')
            ->first();

        // ---------------------------------------------------------
        // 3. INFORME: Canceladas últimamente (Estado 4)
        // ---------------------------------------------------------
        $canceladasRecientes = Res_reserva::withTrashed()
            ->with(['res_inmueble', 'user'])
            ->where('res_status_id', 4)
            ->orderBy('updated_at', 'desc') // Ordenamos por la fecha de actualización/cancelación
            ->take(5)
            ->get();

        return view('reserva.dashboard.index', compact(
            'total_inmuebles', 
            'reservas_pendientes', 
            'reservas_confirmadas', 
            'ultimas_reservas',
            'chartLabels',
            'chartData',
            'diaMasReservas',
            'canceladasRecientes'
        ));
    }

    public function exportarInforme()
    {
        // Traemos todas las reservas, incluyendo las canceladas, con sus relaciones
        $reservas = Res_reserva::withTrashed()
            ->with(['res_inmueble', 'user', 'res_status'])
            ->orderBy('created_at', 'desc')
            ->get();

        $fileName = 'informe_reservas_' . date('Y_m_d_H_i_s') . '.csv';
        
        // Cabeceras para forzar la descarga del archivo
        $headers = array(
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $columns = ['ID', 'Inmueble', 'Asociado', 'Documento', 'Celular', 'Fecha Solicitud', 'Fecha Inicio', 'Fecha Fin', 'Estado'];

        $callback = function() use($reservas, $columns) {
            $file = fopen('php://output', 'w');
            
            // Añadimos el BOM para que Excel reconozca los acentos (UTF-8) correctamente
            fputs($file, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));
            
            // Usamos punto y coma (;) para que Excel separe las columnas automáticamente
            fputcsv($file, $columns, ';');

            foreach ($reservas as $reserva) {
                // Definimos el estado manualmente en caso de que la relación res_status falle
                $estadoNombre = 'Desconocido';
                if($reserva->res_status_id == 1) $estadoNombre = 'Solicitada';
                if($reserva->res_status_id == 2) $estadoNombre = 'Confirmada';
                if($reserva->res_status_id == 3) $estadoNombre = 'Finalizada';
                if($reserva->res_status_id == 4) $estadoNombre = 'Cancelada';
                if($reserva->res_status_id == 5) $estadoNombre = 'Soporte Subido';

                $row = [
                    $reserva->id,
                    $reserva->res_inmueble->name ?? 'N/A',
                    $reserva->user->name ?? 'N/A',
                    $reserva->nid ?? 'N/A',
                    $reserva->celular ?? 'N/A',
                    $reserva->fecha_solicitud ? \Carbon\Carbon::parse($reserva->fecha_solicitud)->format('Y-m-d') : '',
                    $reserva->fecha_inicio ? \Carbon\Carbon::parse($reserva->fecha_inicio)->format('Y-m-d') : '',
                    $reserva->fecha_fin ? \Carbon\Carbon::parse($reserva->fecha_fin)->format('Y-m-d') : '',
                    $estadoNombre
                ];

                fputcsv($file, $row, ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
    public function generarPdf()
    {
        $reservas = Res_reserva::withTrashed()
            ->with(['res_inmueble', 'user', 'res_status'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Calculamos el resumen de estados para el PDF
        $resumenEstados = Res_reserva::withTrashed()
            ->select('res_status_id', DB::raw('count(*) as total'))
            ->groupBy('res_status_id')
            ->with('res_status')
            ->get();

        $fechaInforme = now()->format('d/m/Y H:i');

        // Cargamos una vista específica para el PDF
        $pdf = Pdf::loadView('reserva.dashboard.pdf', compact('reservas', 'resumenEstados', 'fechaInforme'));
        
        // Opcional: Configurar papel (Letter o A4)
        $pdf->setPaper('letter', 'portrait');

        return $pdf->download('informe_reservas_' . now()->format('Ymd_His') . '.pdf');
    }
}