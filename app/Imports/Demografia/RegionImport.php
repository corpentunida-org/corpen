<?php

namespace App\Imports\Demografia;

use App\Models\Demografia\Region;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class RegionImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            Region::updateOrCreate(
                ['id_region' => $row['id_region']],
                [
                    'nombre'     => $row['nombre'],
                    'codigo_iso' => $row['codigo_iso'],
                    'iso_pais'   => $row['iso_pais'],
                ]
            );
        }
    }
}