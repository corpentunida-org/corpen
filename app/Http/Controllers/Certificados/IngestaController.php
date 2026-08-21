<?php

namespace App\Http\Controllers\Certificados;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

// Importación del modelo de Staging
use App\Models\Certificados\CarSiaApi;
// Importación del modelo Core al que se inyectarán los datos
use App\Models\Certificados\CarSiaOperacion;

class IngestaController extends Controller
{
    /**
     * 1. LEE LOTES CRUDOS: Muestra el panel de control del área de Ingesta (Staging)
     */
    public function index()
    {
        try {
            // Se obtienen los registros crudos de la API/ERP, ordenados por los más recientes.
            // Paginamos de a 50 para evitar sobrecarga en la vista técnica.
            $lotesCrudos = CarSiaApi::orderBy('fecha_ad', 'desc')->paginate(50);

            // También podemos calcular estadísticas rápidas para el dashboard técnico
            $totalPendientes = CarSiaApi::where('estado', '!=', 'PROCESADO')->count();

            return view('certificados.ingesta.index', compact('lotesCrudos', 'totalPendientes'));

        } catch (\Exception $e) {
            Log::error('CERTIFICADOS Ingesta - Error al cargar los lotes crudos: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Ocurrió un error al cargar la tabla de staging.');
        }
    }

    /**
     * 2. INYECTA BLOQUES CREADOS: Procesa la tabla de staging y crea las operaciones en el motor
     */
    public function inyectarBloques(Request $request)
    {
        try {
            // Iniciamos la transacción para garantizar que, si algo falla, ningún dato quede a medias.
            DB::transaction(function () {

                // Usamos chunk() para procesar de a 200 registros y no saturar la memoria RAM del servidor
                CarSiaApi::where('estado', 'PENDIENTE') // Ajusta el estado según tu lógica de negocio
                    ->chunk(200, function ($lotes) {

                        foreach ($lotes as $lote) {
                            // Generamos un número de bloque único para agrupar esta transacción
                            $numeroBloque = 'BLQ-' . date('Ymd') . '-' . Str::random(5);

                            // 1. Inyectamos la información cruda al Motor de Operaciones
                            $operacion = CarSiaOperacion::create([
                                'numero_radicado' => 'RAD-' . $lote->id_factura . '-' . time(), // Ajustar según regla de negocio
                                'numero_bloque'   => $numeroBloque,
                                'id_factura'      => $lote->id, // Conecta con el ID del staging (CarSiaApi)
                                'id_tercero'      => $lote->tercero, // Asume que 'tercero' tiene el cod_ter
                            ]);

                            // 2. Marcamos el registro en Staging como procesado
                            // Para evitar que vuelva a ser inyectado en el futuro
                            $lote->update([
                                'estado' => 'PROCESADO'
                            ]);

                            // Nota: Aquí puedes agregar lógica adicional para inyectar en
                            // car_sia_operaciones_lineas si el lote incluye datos de detalle.
                        }
                    });
            });

            Log::info('CERTIFICADOS Ingesta - Inyección de bloques ejecutada correctamente por el usuario ID: ' . Auth::id());
            return redirect()->back()->with('success', 'Los lotes pendientes se han inyectado exitosamente al motor de operaciones.');

        } catch (\Exception $e) {
            Log::error('CERTIFICADOS Ingesta - Error crítico durante la inyección de bloques: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error en la inyección de datos. Se ha revertido el proceso por seguridad.');
        }
    }

    /**
     * 3. ANULAR LOTE: Permite descartar un registro corrupto desde la vista técnica
     */
    public function anularLote(int $id)
    {
        try {
            $lote = CarSiaApi::findOrFail($id);

            // Actualizamos la bandera 'anular' (que definiste en el modelo) a true / 1
            $lote->update(['anular' => 1]);

            Log::warning("CERTIFICADOS Ingesta - Lote de staging ID {$id} anulado manualmente.");

            return redirect()->back()->with('success', 'El lote fue anulado y excluido del próximo procesamiento.');

        } catch (\Exception $e) {
            Log::error("CERTIFICADOS Ingesta - Error al anular lote ID {$id}: " . $e->getMessage());
            return redirect()->back()->with('error', 'No se pudo anular el registro de staging.');
        }
    }
}
