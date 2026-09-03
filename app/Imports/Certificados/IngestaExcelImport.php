<?php

namespace App\Imports\Certificados;

use App\Models\Certificados\CarSiaApi;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class IngestaExcelImport implements ToArray, WithHeadingRow, WithChunkReading
{
    private $nuevoBloque;
    private $ahora;
    private $progresoToken;
    private $filasProcesadas = 0;
    private $totalFilas;

    public function __construct($nuevoBloque, ?string $progresoToken = null, int $totalFilas = 0)
    {
        $this->nuevoBloque = $nuevoBloque;
        $this->ahora = now()->format('Y-m-d H:i:s');
        $this->progresoToken = $progresoToken;
        $this->totalFilas = $totalFilas;
        
        // APAGAMOS EL LOG DE LARAVEL PARA NO SATURAR LA RAM
        DB::disableQueryLog();
    }

    public function array(array $rows)
    {
        if (empty($rows)) return;

        $loteInsercionMasiva = [];

        foreach ($rows as $row) {
            $valores = array_values($row);
            $tercero = $this->valueFromRow($row, ['tercero', 'nit', 'cedula'], 1, $valores);
            $idFactura = $this->valueFromRow($row, ['id_factura', 'factura', 'id_fac'], 0, $valores);

            if (empty($tercero) && empty($idFactura)) {
                continue;
            }

            $valorCelda = $this->valueFromRow($row, ['valor', 'importe', 'monto'], 3, $valores) ?? 0;
            if ($valorCelda !== null) {
                $valorCelda = preg_replace('/[^0-9.-]/', '', (string)$valorCelda);
                $valorCelda = $valorCelda === '' ? 0 : (float)$valorCelda;
            }

            $fechaVenci = $this->valueFromRow($row, ['fecha_venc', 'fecha_venci', 'fecha_vencimiento'], 4, $valores);
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
                'nombre_tercero'   => $this->valueFromRow($row, ['nombre_tercero', 'nombre'], 2, $valores),
                'valor'            => $valorCelda,
                'fecha_venci'      => $fechaVenci,
                'numero_documento' => $this->valueFromRow($row, ['documento', 'numero_documento'], 5, $valores),
                'anio'             => $this->valueFromRow($row, ['ano', 'anio', 'año'], 6, $valores),
                'mes'              => $this->valueFromRow($row, ['mes'], 7, $valores),
                'cuenta'           => $this->valueFromRow($row, ['cuenta'], 8, $valores),
                'banco'            => $this->valueFromRow($row, ['banco'], 9, $valores),
            ];
        }

        if (empty($loteInsercionMasiva)) {
            return;
        }

        $bloquesSeguros = array_chunk($loteInsercionMasiva, 500);

        DB::transaction(function () use ($bloquesSeguros) {
            foreach ($bloquesSeguros as $bloque) {
                CarSiaApi::insert($bloque);
            }
        });

        $this->filasProcesadas += count($rows);
        $this->actualizarProgreso();

        // LIMPIEZA EXTREMA DE MEMORIA PARA EVITAR CAÍDA DE VS CODE
        unset($loteInsercionMasiva);
        unset($bloquesSeguros);
        unset($rows);
        gc_collect_cycles(); // Obliga a PHP a liberar RAM
    }

    private function actualizarProgreso(): void
    {
        if (!$this->progresoToken) return;

        $porcentaje = $this->totalFilas > 0
            ? min(99, (int) floor(($this->filasProcesadas / $this->totalFilas) * 100))
            : 0;

        Cache::put('ingesta_progreso_' . $this->progresoToken, [
            'estado' => 'procesando',
            'procesadas' => $this->filasProcesadas,
            'total' => $this->totalFilas,
            'porcentaje' => $porcentaje,
        ], now()->addHour());
    }

    private function valueFromRow(array $row, array $keys, int $fallbackIndex, array $values): mixed
    {
        foreach ($keys as $key) {
            if (array_key_exists($key, $row) && $row[$key] !== null && $row[$key] !== '') {
                return $row[$key];
            }
        }
        return $values[$fallbackIndex] ?? null;
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}