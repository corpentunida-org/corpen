<?php

namespace App\Imports\Demografia;

use App\Models\Demografia\Pais;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PaisImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // updateOrCreate( [condición de búsqueda], [campos a actualizar/crear] )
            Pais::updateOrCreate(
                ['codigo_iso' => $row['codigo_iso']],
                [
                    'nombre' => $row['nombre'],
                ]
            );
        }
    }
}