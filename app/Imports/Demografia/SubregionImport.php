<?php

namespace App\Imports\Demografia;

use App\Models\Demografia\Subregion;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SubregionImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            Subregion::updateOrCreate(
                ['id_subregion' => $row['id_subregion']],
                [
                    'nombre'    => $row['nombre'],
                    'codigo'    => $row['codigo'],
                    'id_region' => $row['id_region'],
                ]
            );
        }
    }
}