<?php

namespace App\Exports\InformeUso;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

/**
 * Una hoja genérica de filas ya armadas (arrays planos) + encabezados + título — reutilizada
 * para las 4 hojas del Informe de Uso en vez de crear una clase por hoja.
 */
class SimpleArraySheet implements FromCollection, WithHeadings, WithTitle
{
    public function __construct(
        private iterable $filas,
        private array $encabezados,
        private string $titulo,
    ) {
    }

    public function collection()
    {
        return collect($this->filas);
    }

    public function headings(): array
    {
        return $this->encabezados;
    }

    public function title(): string
    {
        return $this->titulo;
    }
}
