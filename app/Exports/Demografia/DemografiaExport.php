<?php

namespace App\Exports\Demografia;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\Demografia\PaisExport;
use App\Exports\Demografia\RegionExport;
use App\Exports\Demografia\SubregionExport;
use App\Exports\Demografia\CiudadExport;
use App\Exports\Demografia\DireccionExport;

class DemografiaExport implements WithMultipleSheets
{
    use Exportable;

    /**
     * Retorna un arreglo con las instancias de cada hoja a exportar.
     */
    public function sheets(): array
    {
        return [
            new PaisExport(),
            new RegionExport(),
            new SubregionExport(),
            new CiudadExport(),
            new DireccionExport(),
        ];
    }
}