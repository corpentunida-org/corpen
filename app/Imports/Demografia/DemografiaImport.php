<?php

namespace App\Imports\Demografia;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Imports\Demografia\PaisImport;
use App\Imports\Demografia\RegionImport;
use App\Imports\Demografia\SubregionImport;
use App\Imports\Demografia\CiudadImport;
use App\Imports\Demografia\DireccionImport;

class DemografiaImport implements WithMultipleSheets
{
    /**
     * Relaciona el nombre de la hoja en Excel con su clase de importación.
     */
    public function sheets(): array
    {
        return [
            // El nombre a la izquierda DEBE coincidir con el nombre de la pestaña en Excel
            'Paises'      => new PaisImport(),
            'Regiones'    => new RegionImport(),
            'Subregiones' => new SubregionImport(),
            'Ciudades'    => new CiudadImport(),
            'Direcciones' => new DireccionImport(),
        ];
    }
}