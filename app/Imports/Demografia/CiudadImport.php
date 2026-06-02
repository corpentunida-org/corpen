<?php

namespace App\Imports\Demografia;

use App\Models\Demografia\Ciudad;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CiudadImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            Ciudad::updateOrCreate(
                ['id_ciudad' => $row['id_ciudad']],
                [
                    'nombre'       => $row['nombre'],
                    'id_subregion' => $row['id_subregion'],
                ]
            );
        }
    }
}