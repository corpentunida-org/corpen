<?php

namespace App\Http\Controllers\Certificados;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use App\Models\Certificados\CarSiaOperacion;
use App\Models\Certificados\CarSiaApi;
use App\Models\Certificados\CarSiaOperacionLinea;

class CertificadoController extends Controller
{
    /**
     * GENERACIÓN INDIVIDUAL (Desde la vista Show)
     */
    public function generarIndividual($id)
    {
        try {
            $operacion = CarSiaOperacion::with('tercero')->findOrFail($id);
            
            // Procesa solo esta operación (Ideal para consultar 1 solo cliente)
            $this->procesarLineasOperacion($operacion);

            // Cargar datos procesados para el PDF
            $lineas = CarSiaOperacionLinea::where('id_car_sia_operaciones', $operacion->id)->get();

            $pdf = Pdf::loadView('certificados.pdf.certificado_aldia', compact('operacion', 'lineas'));
            return $pdf->stream("Certificado_{$operacion->numero_radicado}.pdf");

        } catch (\Exception $e) {
            Log::error("Error al generar certificado individual: " . $e->getMessage());
            return back()->with('error', 'Ocurrió un error al generar el certificado.');
        }
    }

    /**
     * GENERACIÓN MASIVA (Desde la vista Index por Bloque)
     * Ruta: operaciones.pdf_masivo
     */
    public function generarMasivo(Request $request)
    {
        $request->validate(['numero_bloque' => 'required|string']);
        $bloque = $request->numero_bloque;

        try {
            // Transacción para asegurar la integridad de los datos
            DB::beginTransaction();

            $ahora = now();
            $totalOperacionesProcesadas = 0;

            // 1. Procesamiento por bloques (Chunk) de 500 en 500
            CarSiaOperacion::where('numero_bloque', $bloque)
                ->chunkById(500, function ($operacionesChunk) use ($ahora, &$totalOperacionesProcesadas) {
                    
                    // 2. Extraer todos los IDs de terceros para hacer UNA sola consulta a las facturas
                    $tercerosIds = $operacionesChunk->pluck('id_tercero')->toArray();
                    $operacionesMap = $operacionesChunk->keyBy('id_tercero');

                    // 3. Traer todas las facturas de esos terceros de una vez
                    $facturasChunk = CarSiaApi::where('numero_bloque', $operacionesChunk->first()->numero_bloque)
                        ->whereIn('tercero', $tercerosIds)
                        ->get();

                    $lineasAInsertar = [];

                    // 4. Aplicar lógica de negocio en memoria
                    foreach ($facturasChunk as $factura) {
                        $operacion = $operacionesMap[$factura->tercero] ?? null;
                        
                        // Si no hay operación asociada a este tercero, saltamos a la siguiente factura
                        if (!$operacion) continue;

                        $diasMora = 0;
                        if ($factura->fecha_venci) {
                            $fechaVencimiento = Carbon::parse($factura->fecha_venci);
                            $diferencia = $ahora->diffInDays($fechaVencimiento, false);
                            $diasMora = $diferencia < 0 ? abs((int)$diferencia) : 0;
                        }

                        $calificacion = match(true) {
                            $diasMora > 60 => 'Irregular',
                            $diasMora > 30 => 'Regular',
                            default => 'Bueno'
                        };

                        $lineasAInsertar[] = [
                            'id_car_sia_operaciones' => $operacion->id,
                            'id_factura'             => $factura->id,
                            'id_car_sia_lineas'      => $factura->cuenta, // <-- ACTUALIZADO AL NUEVO CAMPO
                            'numero_bloque'          => $operacion->numero_bloque,
                            'observacion'            => "El asociado presenta una calificación $calificacion debido a un registro de $diasMora días de mora.",
                            'calificacion'           => $calificacion,
                            'fecha_venci'            => $factura->fecha_venci,
                            'id_car_sia_estados'     => 3, 
                            'dias_mora_automaticos'  => $diasMora,
                            'procesado_en'           => $ahora->format('Y-m-d H:i:s'),
                        ];
                    }

                    // 5. Upsert Masivo por cada sub-lote para liberar memoria RAM
                    if (!empty($lineasAInsertar)) {
                        collect($lineasAInsertar)->chunk(1000)->each(function ($batch) {
                            CarSiaOperacionLinea::upsert(
                                $batch->toArray(),
                                ['id_car_sia_operaciones', 'id_factura'], 
                                [
                                    'id_car_sia_lineas', // <-- ACTUALIZADO EN EL ARRAY DE UPSERT
                                    'observacion', 
                                    'calificacion', 
                                    'fecha_venci', 
                                    'id_car_sia_estados', 
                                    'dias_mora_automaticos', 
                                    'procesado_en'
                                ]
                            );
                        });
                    }

                    $totalOperacionesProcesadas += $operacionesChunk->count();
                });

            DB::commit();

            return back()->with('success', "Procesamiento masivo completado: Lote $bloque estructurado exitosamente en la base de datos.");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error en procesamiento masivo: " . $e->getMessage() . " en la línea " . $e->getLine());
            
            return back()->with('error', 'Ocurrió un error en la base de datos: ' . $e->getMessage());
        }
    }

    /**
     * MOTOR DE REGLAS: Calcula mora, calificación y llena la tabla (Procesamiento 1 a 1)
     */
    private function procesarLineasOperacion($operacion)
    {
        $facturas = CarSiaApi::where('numero_bloque', $operacion->numero_bloque)
            ->where('tercero', $operacion->id_tercero)
            ->get();

        foreach ($facturas as $factura) {
            $diasMora = 0;

            if ($factura->fecha_venci) {
                $fechaVencimiento = Carbon::parse($factura->fecha_venci);
                $diferencia = now()->diffInDays($fechaVencimiento, false);
                $diasMora = $diferencia < 0 ? abs((int)$diferencia) : 0;
            }

            // REGLAS DE NEGOCIO PARA CALIFICACIÓN
            $calificacion = 'Bueno';
            if ($diasMora > 30 && $diasMora <= 60) {
                $calificacion = 'Regular';
            } elseif ($diasMora > 60) {
                $calificacion = 'Irregular';
            }

            $observacion = "El asociado presenta una calificación $calificacion debido a un registro de $diasMora días de mora.";

            CarSiaOperacionLinea::updateOrCreate(
                [
                    'id_car_sia_operaciones' => $operacion->id,
                    'id_factura'             => $factura->id,
                ],
                [
                    'id_car_sia_lineas'      => $factura->cuenta, // <-- ACTUALIZADO AL NUEVO CAMPO
                    'numero_bloque'          => $operacion->numero_bloque,
                    'observacion'            => $observacion,
                    'calificacion'           => $calificacion,
                    'fecha_venci'            => $factura->fecha_venci,
                    'id_car_sia_estados'     => 3, 
                    'dias_mora_automaticos'  => $diasMora,
                    'procesado_en'           => now(),
                ]
            );
        }
    }
}