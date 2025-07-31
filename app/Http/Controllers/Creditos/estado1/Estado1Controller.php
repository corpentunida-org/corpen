<?php

namespace App\Http\Controllers\Creditos\estado1;

use App\Http\Controllers\Controller; // Asegúrate que esta línea exista
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Estado1Controller extends Controller
{
    // ... aquí puede que tengas otros métodos ...

    /**
     * Muestra la vista del formulario de solicitud.
     * Esta función se encarga de mostrar tu vista 'form.blade.php'.
     */
    public function mostrarFormulario()
    {
        // Apunta a tu vista: /views/creditos/estado1/form.blade.php
        return view('creditos.estado1.form');
    }

    /**
     * Recibe los datos del formulario, calcula la tabla de amortización
     * y muestra la vista de resultados.
     */
    public function calcularAmortizacion(Request $request)
    {
        // 1. Validar los datos que vienen del formulario
        $validator = Validator::make($request->all(), [
            'valor_solicitado' => 'required|numeric|min:1',
            'plazo_solicitado' => 'required|integer|min:1',
        ],[
            'valor_solicitado.required' => 'El valor solicitado es obligatorio.',
            'plazo_solicitado.required' => 'El plazo en meses es obligatorio.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                        ->withErrors($validator)
                        ->withInput();
        }

        // 2. Obtener los valores del formulario
        $monto = (float) $request->input('valor_solicitado');
        $plazo = (int) $request->input('plazo_solicitado');
        $tasaInteresMensual = 0.01; // 1% de interés fijo

        // 3. Calcular la cuota fija mensual (Fórmula de Anualidad)
        $cuotaMensual = ($tasaInteresMensual > 0)
            ? $monto * ($tasaInteresMensual * pow(1 + $tasaInteresMensual, $plazo)) / (pow(1 + $tasaInteresMensual, $plazo) - 1)
            : $monto / $plazo;
        
        // 4. Generar la tabla de amortización
        $tabla = [];
        $saldoPendiente = $monto;
        $totalInteres = 0;

        for ($i = 1; $i <= $plazo; $i++) {
            $interesCuota = $saldoPendiente * $tasaInteresMensual;
            $abonoCapital = $cuotaMensual - $interesCuota;
            $saldoPendiente -= $abonoCapital;
            
            // Ajuste para la última cuota para que el saldo final sea exactamente 0.00
            if ($i == $plazo) {
                $abonoCapital = $abonoCapital + $saldoPendiente;
                $cuotaMensual = $abonoCapital + $interesCuota;
                $saldoPendiente = 0;
            }

            $tabla[] = [
                'numero_cuota' => $i,
                'valor_cuota' => $cuotaMensual,
                'interes' => $interesCuota,
                'abono_capital' => $abonoCapital,
                'saldo_final' => $saldoPendiente,
            ];
            
            $totalInteres += $interesCuota;
        }

        // 5. Enviar los datos a la vista de la tabla de amortización
        // Apunta a tu vista: /views/creditos/estado1/tabladeamortizacion.blade.php
        return view('creditos.estado1.tabladeamortizacion', [
            'tabla' => $tabla,
            'monto_solicitado' => $monto,
            'plazo_meses' => $plazo,
            'tasa_interes' => $tasaInteresMensual * 100,
            'total_interes' => $totalInteres,
            'total_pagar' => $monto + $totalInteres,
        ]);
    }

    // ... aquí pueden seguir otros métodos ...
}