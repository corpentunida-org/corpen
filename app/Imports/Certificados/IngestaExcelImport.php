<?php

namespace App\Imports\Certificados;

use App\Models\Certificados\CarSiaApi;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Contracts\Queue\ShouldQueue;

class IngestaExcelImport implements ToCollection, WithChunkReading, WithHeadingRow, ShouldQueue
{
    private $nuevoBloque;
    private $ahora;

    public function __construct($nuevoBloque)
    {
        $this->nuevoBloque = $nuevoBloque;
        $this->ahora = now()->format('Y-m-d H:i:s');
    }

    public function collection(Collection $rows)
    {
        $loteInsercionMasiva = [];

        foreach ($rows as $row) {
            // Ignorar filas completamente vacías
            if (!isset($row['tercero']) && !isset($row['id_factura'])) {
                continue;
            }

            // Limpieza del valor monetario
            $valorCelda = $row['valor'] ?? 0;
            if ($valorCelda !== null) {
                $valorCelda = preg_replace('/[^0-9.-]/', '', (string)$valorCelda);
                $valorCelda = $valorCelda === '' ? 0 : (float)$valorCelda;
            }

            // Limpieza de fecha (Laravel Excel con WithHeadingRow formatea las cabeceras automáticamente)
            $fechaVenci = $row['fecha_venc'] ?? $row['fecha_venci'] ?? null;
            if (is_numeric($fechaVenci)) {
                try {
                    $fechaVenci = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($fechaVenci)->format('Y-m-d');
                } catch (\Exception $e) {
                    $fechaVenci = null;
                }
            }

            $loteInsercionMasiva[] = [
                'estado'           => 'PENDIENTE',
                'fecha_ad'         => $this->ahora,
                'created_at'       => $this->ahora,
                'updated_at'       => $this->ahora,
                'numero_bloque'    => $this->nuevoBloque,
                'id_factura'       => $row['id_factura'] ?? null,
                'tercero'          => $row['tercero'] ?? null,
                'nombre_tercero'   => $row['nombre_tercero'] ?? null,
                'valor'            => $valorCelda,
                'fecha_venci'      => $fechaVenci,
                // El slug automático de Laravel elimina el # y cambia la ñ
                'numero_documento' => $row['documento'] ?? $row['numero_documento'] ?? null,
                'anio'             => $row['ano'] ?? $row['anio'] ?? null,
                'mes'              => $row['mes'] ?? null,
                'cuenta'           => $row['cuenta'] ?? null,
                'banco'            => $row['banco'] ?? null,
            ];
        }

        // Insertar el chunk actual de golpe en la base de datos
        if (!empty($loteInsercionMasiva)) {
            CarSiaApi::insert($loteInsercionMasiva);
        }
    }

    public function chunkSize(): int
    {
        return 1000; // Libera la RAM cada 1.000 filas procesadas
    }
}
