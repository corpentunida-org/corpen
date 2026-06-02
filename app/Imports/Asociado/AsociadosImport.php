<?php

namespace App\Imports\Asociado;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class AsociadosImport implements ToCollection, WithHeadingRow
{
    protected $rows = [];

    /**
     * Almacena las filas leídas transformándolas en array asociativo por el HeadingRow.
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        $this->rows = $collection->toArray();
    }

    /**
     * Obtiene las filas procesadas.
     * @return array
     */
    public function getRows()
    {
        return $this->rows;
    }
}