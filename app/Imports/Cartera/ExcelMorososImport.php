<?php

namespace App\Imports\Cartera;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class ExcelMorososImport implements ToCollection
{
    protected $rows = [];
    /**
    * @param Collection $collection
    * @return void
    */
    public function collection(Collection $collection)
    {
        $this->rows = $collection->toArray();
    }

    /**
     *
     * @return array
     */
    public function getRows()
    {
        return $this->rows;
    }

}
