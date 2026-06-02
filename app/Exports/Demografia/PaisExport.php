<?php

namespace App\Exports\Demografia;

use App\Models\Demografia\Pais;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class PaisExport implements FromCollection, WithHeadings, WithTitle
{
    public function collection()
    {
        return Pais::select(['codigo_iso', 'nombre'])->get();
    }

    public function headings(): array
    {
        return ['codigo_iso', 'nombre'];
    }

    public function title(): string
    {
        return 'Paises';
    }
}