<?php

namespace App\Http\Controllers\Contabilidad;

use App\Http\Controllers\Controller;
use App\Models\Contabilidad\ConExtractoTransaccion;
use App\Models\Contabilidad\ConCuentaBancaria;
use Illuminate\Http\Request;

class ConExtractoTransaccionController extends Controller
{
    public function index()
    {
        // Traemos los extractos junto con su cuenta asociada
        $extractos = ConExtractoTransaccion::with('cuentaBancaria')
                        ->orderBy('fecha_movimiento', 'desc')
                        ->get();

        // Traemos las cuentas activas
        $cuentas = ConCuentaBancaria::where('estado', 'Activa')->get();

        return view('contabilidad.extractos.index', compact('extractos', 'cuentas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_con_cuentas_bancaria' => 'required|exists:con_cuentas_bancarias,id',
            'hash_transaccion'        => 'required|string|unique:con_extractos_transacciones,hash_transaccion',
            'fecha_movimiento'        => 'required|date',
            'valor_ingreso'           => 'required|integer',
            'descripcion_banco'       => 'required|string',
            'estado_conciliacion'     => 'required|in:Pendiente,Conciliado_Auto,Conciliado_Manual,Anulado',
        ]);

        ConExtractoTransaccion::create($validated);

        return redirect()->route('contabilidad.extractos.index')
                         ->with('success', 'El movimiento del extracto fue registrado exitosamente.');
    }

    public function show($id)
    {
        $extracto = ConExtractoTransaccion::with('cuentaBancaria')->findOrFail($id);
        return view('contabilidad.extractos.show', compact('extracto'));
    }

    public function update(Request $request, $id)
    {
        $transaccion = ConExtractoTransaccion::findOrFail($id);

        $validated = $request->validate([
            'estado_conciliacion' => 'sometimes|in:Pendiente,Conciliado_Auto,Conciliado_Manual,Anulado',
            'descripcion_banco'   => 'sometimes|string',
        ]);

        $transaccion->update($validated);

        return redirect()->route('contabilidad.extractos.index')
                         ->with('success', 'El estado del movimiento ha sido actualizado.');
    }

    public function destroy($id)
    {
        $transaccion = ConExtractoTransaccion::findOrFail($id);
        $transaccion->delete();

        return redirect()->route('contabilidad.extractos.index')
                         ->with('success', 'Movimiento eliminado correctamente.');
    }

    // ==========================================
    // MÉTODOS NUEVOS PARA LAS VISTAS
    // ==========================================

    public function importar()
    {
        // Traemos las cuentas activas para el select del formulario
        $cuentas = ConCuentaBancaria::where('estado', 'Activa')->get();
        
        return view('contabilidad.extractos.importar', compact('cuentas'));
    }

    public function conciliacion()
    {
        // 1. Lado Izquierdo: Extractos del banco pendientes
        $extractosPendientes = ConExtractoTransaccion::with('cuentaBancaria')
                                ->where('estado_conciliacion', 'Pendiente')
                                ->orderBy('fecha_movimiento', 'desc')
                                ->get();

        // 2. Lado Derecho: Soportes de cartera (pagos registrados)
        // Traemos los recibos ordenados por fecha
        $comprobantesCartera = \App\Models\Cartera\CarComprobantePago::orderBy('id', 'desc')->get();

        return view('contabilidad.extractos.conciliacion', compact('extractosPendientes', 'comprobantesCartera'));
    }

    /**
     * Recibe el archivo CSV, lo lee y lo guarda en la base de datos.
     */
    public function procesarImportacion(Request $request)
    {
        // 1. Validamos que seleccione la cuenta y suba un archivo (CSV/TXT)
        $request->validate([
            'id_con_cuentas_bancaria' => 'required|exists:con_cuentas_bancarias,id',
            'archivo_extracto'        => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $idCuenta = $request->id_con_cuentas_bancaria;
        $archivo = $request->file('archivo_extracto');

        // 2. Abrimos el archivo CSV en modo lectura
        $fileHandle = fopen($archivo->getRealPath(), "r");
        
        $isHeader = true;
        $registrosImportados = 0;

        // 3. Leemos el archivo línea por línea (fgetcsv separa por comas automáticamente)
        while (($row = fgetcsv($fileHandle, 1000, ",")) !== false) {
            
            // Saltamos la primera fila porque contiene los títulos de las columnas
            if ($isHeader) {
                $isHeader = false;
                continue;
            }

            // 4. Verificamos que la fila tenga al menos las 4 columnas de nuestro ejemplo
            if (count($row) >= 4) {
                
                // Mapeo según el CSV que creamos:
                // $row[0] = fecha_movimiento
                // $row[1] = descripcion_banco
                // $row[2] = hash_transaccion
                // $row[3] = valor_ingreso

                // 5. Usamos updateOrCreate para EVITAR DUPLICADOS. 
                // Busca si el hash ya existe. Si existe, no hace nada nuevo. Si no existe, lo inserta.
                ConExtractoTransaccion::updateOrCreate(
                    [
                        'hash_transaccion' => $row[2] // Llave única de búsqueda
                    ], 
                    [
                        'id_con_cuentas_bancaria' => $idCuenta,
                        'fecha_movimiento'        => trim($row[0]),
                        'descripcion_banco'       => trim($row[1]),
                        'valor_ingreso'           => (int) trim($row[3]),
                        'estado_conciliacion'     => 'Pendiente', // Todo entra como pendiente
                    ]
                );
                
                $registrosImportados++;
            }
        }

        // Cerramos el archivo para liberar memoria
        fclose($fileHandle);

        // 6. Redireccionamos con el mensaje de cuántos se guardaron
        return redirect()->route('contabilidad.extractos.index')
                         ->with('success', "¡Importación exitosa! Se procesaron $registrosImportados movimientos nuevos.");
    }

}