<?php

namespace App\Imports\Demografia;

use App\Models\Demografia\Direccion;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class DireccionImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            Direccion::updateOrCreate(
                ['id_direccion' => $row['id_direccion']],
                [
                    'calle'         => $row['calle'],
                    'numero'        => $row['numero'],
                    'codigo_postal' => $row['codigo_postal'],
                    'id_ciudad'     => $row['id_ciudad'],
                ]
            );
        }
    }
}