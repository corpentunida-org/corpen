<?php

namespace App\Imports;

use App\Models\Seguros\SegPoliza;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ExcelImport implements ToModel, WithHeadingRow
{
    /**
     * Este método se ejecutará para cada fila en el archivo Excel.
     * 
     * @param array $row
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new SegPoliza([
            'seg_asegurado_id' => $row['COD_TER'],
            'valorpagaraseguradora' => $row['DEB_MOV'],
        ]);
    }
}