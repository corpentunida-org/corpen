<?php

namespace App\Exports\Demografia;

use App\Models\Demografia\Ciudad;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class CiudadExport implements FromCollection, WithHeadings, WithTitle
{
    public function collection()
    {
        return Ciudad::select(['id_ciudad', 'nombre', 'id_subregion'])->get();
    }

    public function headings(): array
    {
        return ['id_ciudad', 'nombre', 'id_subregion'];
    }

    public function title(): string
    {
        return 'Ciudades';
    }
}