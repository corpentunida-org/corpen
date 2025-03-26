<?php

namespace App\Imports;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ExcelExport implements FromCollection, WithHeadings
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }
    public function headings(): array
    {
        return [
            'POLIZA', 'ID', 'NOMBRE', 'NUM DOC', 'FECHA NAC', 'GENERO', 'EDAD', 'DOC AF','PARENTESCO', 'FEC NOVEDAD', 'VALOR ASEGURADO','EXTRA PRIMA', 'PRIMA'
        ];
    }

    public function collection()
    {
        return collect($this->data);
    }
}