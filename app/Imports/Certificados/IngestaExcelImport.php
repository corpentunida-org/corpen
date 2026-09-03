<?php

namespace App\Imports\Certificados;

use App\Models\Certificados\CarSiaApi;
use App\Models\Certificados\CarSiaBloque;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Events\ImportFailed;

class IngestaExcelImport implements ToArray, WithChunkReading, WithHeadingRow, ShouldQueue, WithEvents
{
    private $nuevoBloque;
    private $rutaArchivo;
    private $ahora;

    public function __construct($nuevoBloque, $rutaArchivo = null)
    {
        $this->nuevoBloque = $nuevoBloque;
        $this->rutaArchivo = $rutaArchivo;
        $this->ahora = now()->format('Y-m-d H:i:s');
    }

    // Usar array() en lugar de collection() reduce drásticamente el consumo de RAM
    public function array(array $rows)
    {
        $loteInsercionMasiva = [];

        foreach ($rows as $row) {
            // Validación robusta de fila vacía
            if (empty($row['tercero']) && empty($row['id_factura'])) {
                continue;
            }

            $valorCelda = isset($row['valor']) ? preg_replace('/[^0-9.-]/', '', (string)$row['valor']) : 0;
            $valorCelda = $valorCelda === '' ? 0 : (float)$valorCelda;

            $fechaVenci = $row['fecha_venc'] ?? $row['fecha_venci'] ?? null;
            if (is_numeric($fechaVenci)) {
                try {
                    $fechaVenci = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($fechaVenci)->format('Y-m-d');
                } catch (\Throwable $e) {
                    $fechaVenci = null; // Failsafe para fechas corruptas
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
                'numero_documento' => $row['documento'] ?? $row['numero_documento'] ?? null,
                'anio'             => $row['ano'] ?? $row['anio'] ?? null,
                'mes'              => $row['mes'] ?? null,
                'cuenta'           => $row['cuenta'] ?? null,
                'banco'            => $row['banco'] ?? null,
            ];
        }

        if (!empty($loteInsercionMasiva)) {
            CarSiaApi::insert($loteInsercionMasiva);
        }
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    // Hooks para automatizar la limpieza y cambiar el estado del bloque padre
    public function registerEvents(): array
    {
        return [
            AfterImport::class => function (AfterImport $event) {
                CarSiaBloque::where('numero_bloque', $this->nuevoBloque)->update(['estado' => 'PROCESADO']);

                if ($this->rutaArchivo && Storage::exists($this->rutaArchivo)) {
                    Storage::delete($this->rutaArchivo);
                }
            },
            ImportFailed::class => function (ImportFailed $event) {
                CarSiaBloque::where('numero_bloque', $this->nuevoBloque)->update(['estado' => 'ERROR']);
                Log::error("CERTIFICADOS Ingesta - Error en bloque {$this->nuevoBloque}: " . $event->getException()->getMessage());
            },
        ];
    }
}
