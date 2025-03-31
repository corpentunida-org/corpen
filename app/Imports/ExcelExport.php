<?php

namespace App\Imports;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ExcelExport implements FromCollection, WithHeadings
{
    protected $data;
    protected $headings;

    public function __construct($data, $headings = null)
    {
        $this->data = $data;
        $this->headings = $headings ?: [
            'ID',
        ];
    }
    public function headings(): array
    {
        return $this->headings;
    }

    public function collection()
    {
        return collect($this->data);
    }
}