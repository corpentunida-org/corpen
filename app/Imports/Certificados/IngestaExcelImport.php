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
                // --- Campos de control generados por sistema ---
                'estado'           => 'PENDIENTE',
                'fecha_ad'         => $this->ahora,
                'created_at'       => $this->ahora,
                'updated_at'       => $this->ahora,
                'numero_bloque'    => $this->nuevoBloque,

                // --- Campos que ya tenías mapeados ---
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

                // --- ¡NUEVOS! Campos agregados para completar la tabla ---
                // Nota: El número al final de valueFromRow (ej: 10) es la columna de respaldo si no encuentra el encabezado.
                'is_selected'      => $this->valueFromRow($row, ['is_selected'], 10, $valores) ?? null,
                'detalle'          => $this->valueFromRow($row, ['detalle'], 11, $valores) ?? null,
                'log_rq'           => $this->valueFromRow($row, ['log_rq'], 12, $valores) ?? null,
                'anular'           => $this->valueFromRow($row, ['anular'], 13, $valores) ?? null,
                'nombre_cuenta'    => $this->valueFromRow($row, ['nombre_cuenta'], 14, $valores) ?? null,
                'tercero_base'     => $this->valueFromRow($row, ['tercero_base'], 15, $valores) ?? null,
                'tercero_cco'      => $this->valueFromRow($row, ['tercero_cco'], 16, $valores) ?? null,
                'doc_mov'          => $this->valueFromRow($row, ['doc_mov'], 17, $valores) ?? null,
                'cco'              => $this->valueFromRow($row, ['cco'], 18, $valores) ?? null,
                'trn'              => $this->valueFromRow($row, ['trn'], 19, $valores) ?? null,
                'pagare'           => $this->valueFromRow($row, ['pagare'], 20, $valores) ?? null,
                'cuota'            => $this->valueFromRow($row, ['cuota'], 21, $valores) ?? null,
                'contabilizado'    => $this->valueFromRow($row, ['contabilizado'], 22, $valores) ?? null,
                'nota'             => $this->valueFromRow($row, ['nota'], 23, $valores) ?? null,
                'fecha_trn_banco'  => $this->valueFromRow($row, ['fecha_trn_banco'], 24, $valores) ?? null,
                'valor_inicial'    => $this->valueFromRow($row, ['valor_inicial'], 25, $valores) ?? null,
                'valor_pago_ofic'  => $this->valueFromRow($row, ['valor_pago_ofic'], 26, $valores) ?? null,
                'valor_banco'      => $this->valueFromRow($row, ['valor_banco'], 27, $valores) ?? null,
                'uid_banco'        => $this->valueFromRow($row, ['uid_banco'], 28, $valores) ?? null,
                'fecha_edit'       => $this->valueFromRow($row, ['fecha_edit'], 29, $valores) ?? null,
                'tipo'             => $this->valueFromRow($row, ['tipo'], 30, $valores) ?? null,
                'id_cab'           => $this->valueFromRow($row, ['id_cab'], 31, $valores) ?? null,
                'id_reg_cab_ref'   => $this->valueFromRow($row, ['id_reg_cab_ref'], 32, $valores) ?? null,
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
