<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReporteSiniestrosExport implements FromCollection, WithHeadings, WithStyles
{
    protected $data;

    public function __construct(Collection $data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        return [
            'CEDULA',
            'NOMBRE',
            'DTO',
            'AMPARO',
            'VALOR COBERTURA',
            'BONO CANASTA',
            'TOTAL DESEMBOLSADO',
            'FECHA RADICADO',
            'FECHA DESEMBOLSO',
            'DIAS HABILES'
        ];
    }

    // Aquí definimos los estilos
    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();

        return [
            // Encabezados en negrita y con fondo gris
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'color' => ['rgb' => 'D9D9D9']
                ],
            ],

            // Fila de resumen (última fila) en negrita y con fondo amarillo
            $lastRow => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'color' => ['rgb' => 'FFF2CC']
                ],
            ],
        ];
    }
}
