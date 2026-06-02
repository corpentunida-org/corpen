<?php

namespace App\Exports\Demografia;

use App\Models\Demografia\Region;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class RegionExport implements FromCollection, WithHeadings, WithTitle
{
    public function collection()
    {
        return Region::select(['id_region', 'nombre', 'codigo_iso', 'iso_pais'])->get();
    }

    public function headings(): array
    {
        return ['id_region', 'nombre', 'codigo_iso', 'iso_pais'];
    }

    public function title(): string
    {
        return 'Regiones';
    }
}