<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ExportSegInformeReclamacion implements FromArray, WithStyles, WithEvents
{
    protected $registrosPorEstado;

    public function __construct($registrosPorEstado)
    {
        $this->registrosPorEstado = $registrosPorEstado;
    }

    public function array(): array
    {
        $output = [];
        foreach ($this->registrosPorEstado as $estado => $registros) {
            $output[] = ["SINIESTROS ESTADO {$estado}"];
            if ($estado === 'OBJETADO') {
                $output[] = ['CÉDULA', 'NOMBRE', 'DISTRITO', 'GENERO', 'COBERTURA', 'CAUSA', 'OBSERVACIÓN'];
                $observacion = $registro->cambiosEstado->firstWhere('estado_id', $registro->estado)->observacion ?? '';
                foreach ($registros as $registro) {
                    $output[] = [
                        $registro->cedulaAsegurado,
                        $registro->nombre_tercero,
                        $registro->tercero?->cod_dist != 0 ? substr($registro->tercero->cod_dist, -2) : '',
                        $registro->tercero->sexo ?? $registro->terceroAlt->sexo ?? 'No definido',
                        $registro->cobertura->nombre ?? '',                   
                        $registro->diagnostico->diagnostico ?? $registro->otro,
                        $observacion
                    ];
                }
            }else{
                $output[] = ['CÉDULA', 'NOMBRE', 'DISTRITO','GENERO', 'COBERTURA', 'VALOR ASEGURADO', 'CAUSA'];                
                foreach ($registros as $registro) {
                    $output[] = [
                        $registro->cedulaAsegurado,
                        $registro->nombre_tercero,
                        $registro->tercero?->cod_dist != 0 ? substr($registro->tercero->cod_dist, -2) : '',
                        $registro->tercero->sexo ?? $registro->terceroAlt->sexo ?? 'No definido',
                        $registro->cobertura->nombre ?? '',
                        $registro->valor_asegurado ?? '',
                        $registro->diagnostico->diagnostico ?? $registro->otro
                        
                    ];
                }
            }
            
            $output[] = ['', '', '', '', '', ''];
        }

        return $output;
    }

    public function styles(Worksheet $sheet)
    {
        // Solo aplica estilos genéricos aquí
        return [
            // Encabezados de columnas en negrita
            2 => ['font' => ['bold' => true]]
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $row = 1;

                foreach ($this->registrosPorEstado as $estado => $registros) {
                    // Detectar columnas de cada bloque
                    $numCols = ($estado === 'OBJETADO') ? 7 : 7;
                    $lastCol = Coordinate::stringFromColumnIndex($numCols);

                    // Estilo para el encabezado de estado
                    $sheet->mergeCells("A{$row}:{$lastCol}{$row}");
                    $sheet->getStyle("A{$row}:{$lastCol}{$row}")->applyFromArray([
                        'font' => [
                            'bold' => true,
                            'size' => 12,
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                            'vertical'   => Alignment::VERTICAL_CENTER,
                        ],
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'color'    => ['rgb' => 'D9D9D9'], // gris
                        ],
                    ]);

                    $row++; // fila de encabezados

                    // Encabezados en negrilla
                    $sheet->getStyle("A{$row}:{$lastCol}{$row}")->applyFromArray([
                        'font' => ['bold' => true],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                            'vertical'   => Alignment::VERTICAL_CENTER,
                        ]
                    ]);

                    // Avanza filas = registros + fila vacía
                    $row += count($registros) + 2;
                }
            }
        ];
    }
}
