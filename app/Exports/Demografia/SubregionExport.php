<?php

namespace App\Exports\Demografia;

use App\Models\Demografia\Subregion;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class SubregionExport implements FromCollection, WithHeadings, WithTitle
{
    public function collection()
    {
        return Subregion::select(['id_subregion', 'nombre', 'codigo', 'id_region'])->get();
    }

    public function headings(): array
    {
        return ['id_subregion', 'nombre', 'codigo', 'id_region'];
    }

    public function title(): string
    {
        return 'Subregiones';
    }
}