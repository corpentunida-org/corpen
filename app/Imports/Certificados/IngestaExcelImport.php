<?php

namespace App\Imports\Certificados;

use App\Models\Certificados\CarSiaApi;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class IngestaExcelImport implements ToArray, WithChunkReading, WithHeadingRow
{
    private $nuevoBloque;
    private $ahora;

    public function __construct($nuevoBloque)
    {
        $this->nuevoBloque = $nuevoBloque;
        $this->ahora = now()->format('Y-m-d H:i:s');
    }

    public function array(array $rows)
    {
        if (empty($rows)) return;

        $loteInsercionMasiva = [];

        foreach ($rows as $row) {
            // Buscamos la columna tercero con varios nombres posibles
            $tercero = $row['tercero'] ?? $row['nit'] ?? $row['cedula'] ?? $row['documento'] ?? null;
            $idFactura = $row['id_factura'] ?? $row['factura'] ?? $row['id_fac'] ?? null;

            if (empty($tercero) && empty($idFactura)) {
                continue;
            }

            $valorCelda = $row['valor'] ?? 0;
            if ($valorCelda !== null) {
                $valorCelda = preg_replace('/[^0-9.-]/', '', (string)$valorCelda);
                $valorCelda = $valorCelda === '' ? 0 : (float)$valorCelda;
            }

            $fechaVenci = $row['fecha_venc'] ?? $row['fecha_venci'] ?? null;
            if (is_numeric($fechaVenci)) {
                try {
                    $fechaVenci = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($fechaVenci)->format('Y-m-d');
                } catch (\Throwable $e) {
                    $fechaVenci = null;
                }
            }

            $loteInsercionMasiva[] = [
                'estado'           => 'PENDIENTE',
                'fecha_ad'         => $this->ahora,
                'created_at'       => $this->ahora,
                'updated_at'       => $this->ahora,
                'numero_bloque'    => $this->nuevoBloque,
                'id_factura'       => $idFactura,
                'tercero'          => $tercero,
                'nombre_tercero'   => $row['nombre_tercero'] ?? $row['nombre'] ?? null,
                'valor'            => $valorCelda,
                'fecha_venci'      => $fechaVenci,
                'numero_documento' => $row['documento'] ?? $row['numero_documento'] ?? null,
                'anio'             => $row['ano'] ?? $row['anio'] ?? null,
                'mes'              => $row['mes'] ?? null,
                'cuenta'           => $row['cuenta'] ?? null,
                'banco'            => $row['banco'] ?? null,
            ];
        }

        // 🚨 EL SEGURO: Si el lote quedó vacío, aborta y muestra los títulos reales
        if (empty($loteInsercionMasiva)) {
            $cabecerasDetectadas = implode(', ', array_keys($rows[0]));
            throw new \Exception("ERROR DE TÍTULOS: Laravel detectó estos títulos en tu Excel [ {$cabecerasDetectadas} ] y no coinciden con 'tercero' ni 'id_factura'.");
        }

        CarSiaApi::insert($loteInsercionMasiva);
    }

    public function chunkSize(): int
    {
        return 100;
    }
}
