<?php

namespace App\Exports\Demografia;

use App\Models\Demografia\Direccion;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class DireccionExport implements FromCollection, WithHeadings, WithTitle
{
    public function collection()
    {
        return Direccion::select(['id_direccion', 'calle', 'numero', 'codigo_postal', 'id_ciudad'])->get();
    }

    public function headings(): array
    {
        return ['id_direccion', 'calle', 'numero', 'codigo_postal', 'id_ciudad'];
    }

    public function title(): string
    {
        return 'Direcciones';
    }
}