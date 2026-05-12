<?php

namespace App\Imports;

use App\Models\Contabilidad\ConExtractoTransaccion;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TransaccionesImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        $datosParaUpsert = [];

        foreach ($rows as $row) {
            if (empty($row['id_transaccion'])) {
                continue;
            }

            // Procesamiento de fecha seguro
            $fecha = null;
            if (isset($row['fecha_movimiento'])) {
                $fecha = is_numeric($row['fecha_movimiento']) 
                    ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['fecha_movimiento'])->format('Y-m-d H:i:s') 
                    : Carbon::parse($row['fecha_movimiento'])->format('Y-m-d H:i:s');
            }

            $datosParaUpsert[] = [
                'id_transaccion'          => $row['id_transaccion'],
                'fecha_movimiento'        => $fecha,
                'referencia_cedula'       => $row['referencia_cedula'] ?? null,
                'valor_ingreso'           => isset($row['valor_ingreso']) ? (float)$row['valor_ingreso'] : null,
                'referencia_oficina'      => $row['referencia_oficina'] ?? null,
                'id_con_cuentas_bancaria' => $row['id_con_cuentas_bancaria'] ?? null,
                'estado_conciliacion'     => $row['estado_conciliacion'] ?? 'Pendiente',
                'hash_transaccion'        => $row['hash_transaccion'] ?? null,
            ];
        }

        if (!empty($datosParaUpsert)) {
            // MEJORA: Dividimos el array gigante en trozos de 1,000 filas
            $lotes = array_chunk($datosParaUpsert, 1000);

            DB::transaction(function () use ($lotes) {
                foreach ($lotes as $lote) {
                    ConExtractoTransaccion::upsert(
                        $lote,
                        ['id_transaccion'], // Único por ID
                        [
                            'fecha_movimiento', 
                            'referencia_cedula', 
                            'valor_ingreso', 
                            'referencia_oficina', 
                            'id_con_cuentas_bancaria', 
                            'estado_conciliacion', 
                            'hash_transaccion'
                        ]
                    );
                }
            });
        }
    }
}