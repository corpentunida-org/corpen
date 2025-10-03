<?php

namespace App\Imports;

use App\Models\Maestras\maeTerceros;
use App\Models\Seguros\SegAsegurado;
use App\Models\Seguros\SegPoliza;
use App\Models\Seguros\SegTercero;
use App\Models\Seguros\SegPlan;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Carbon\Carbon;

class PolizasImport implements ToCollection, WithHeadingRow, WithChunkReading
{
    public $updatedCount = 0;
    public $failedRows = [];

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // --- Validaciones ---
            if (!in_array(strtoupper($row['genero']), ['V', 'H'])) {
                $this->failedRows[] = [
                    'cedula' => $row['num_doc'],
                    'obser'  => 'Género inválido. Debe ser V o H.',
                ];
                continue;
            }

            if (!in_array(strtoupper($row['parentesco']), ['AF', 'CO', 'HI', 'HE'])) {
                $this->failedRows[] = [
                    'cedula' => $row['num_doc'],
                    'obser'  => 'Parentesco inválido. Debe ser AF, CO, HI o HE.',
                ];
                continue;
            }

            // --- Fecha ---
            try {
                $fechaNacimiento = Carbon::create(1899, 12, 30)->addDays($row['fecha_nac'])->toDateString();
                $edad = Carbon::parse($fechaNacimiento)->age;
                if ($edad < 0 || $edad > 120) {
                    throw new \Exception("Edad inválida: $edad años.");
                }
            } catch (\Exception $e) {
                $this->failedRows[] = [
                    'cedula' => $row['num_doc'],
                    'obser'  => 'Fecha de nacimiento inválida',
                ];
                continue;
            }

            // --- Tercero ---
            $tercero = SegTercero::updateOrCreate(
                ['cedula' => $row['num_doc']],
                [
                    'nombre'          => $row['nombre'],
                    'fechaNacimiento' => $fechaNacimiento,
                    'genero'          => $row['genero'],
                ]
            );

            // --- maeTerceros ---
            maeTerceros::updateOrCreate(
                ['cod_ter' => $row['num_doc']],
                [
                    'nom_ter' => $row['nombre'],
                    'fec_nac' => $fechaNacimiento,
                    'sexo'    => $row['genero'],
                ]
            );

            // --- Asegurado ---
            $asegurado = SegAsegurado::updateOrCreate(
                ['cedula' => $tercero->cedula],
                [
                    'parentesco'        => $row['parentesco'],
                    'titular'           => $row['titular'] ?? $tercero->cedula,
                    'valorpAseguradora' => $row['valor_titular'] ?? null,
                ]
            );

            // --- Plan ---
            $condicion_id = app(\App\Http\Controllers\Seguros\SegPlanController::class)->getCondicion($edad);
            $plan_id = SegPlan::select('id')
                ->where('vigente', true)
                ->where('condicion_corpen', $condicion_id)
                ->where('valor', $row['valor_asegurado'])
                ->first();
            $plan_id_value = $plan_id ? $plan_id->id : 77;

            // --- Extra Prima ---
            $extraPrima = isset($row['extra_prim']) ? intval($row['extra_prim'] * 100) : 0;

            // --- Póliza ---
            SegPoliza::updateOrCreate(
                ['seg_asegurado_id' => $asegurado->cedula],
                [
                    'seg_convenio_id'       => $row['poliza'],
                    'active'                => true,
                    'fecha_inicio'          => Carbon::now()->toDateString(),
                    'seg_plan_id'           => $plan_id_value,
                    'valor_asegurado'       => $row['valor_asegurado'],
                    'valor_prima'           => $row['prima'],
                    'primapagar'            => $row['prima_corpen'],
                    'extra_prima'           => $extraPrima,
                    'valorpagaraseguradora' => $row['valor_titular'] ?? null,
                ]
            );

            $this->updatedCount++;
        }
    }

    public function chunkSize(): int
    {
        return 1000; // procesa de a 1000 filas
    }
}
