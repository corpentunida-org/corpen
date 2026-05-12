<?php

namespace App\Exports;

use App\Models\Contabilidad\ConExtractoTransaccion;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TransaccionesExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return ConExtractoTransaccion::select([
            'id_transaccion',
            'fecha_movimiento',
            'referencia_cedula',
            'valor_ingreso',
            'referencia_oficina',
            'id_con_cuentas_bancaria',
            'estado_conciliacion',
            'hash_transaccion',
            'created_at',
            'updated_at'
        ])->get();
    }

    public function headings(): array
    {
        return [
            'id_transaccion',
            'fecha_movimiento',
            'referencia_cedula',
            'valor_ingreso',
            'referencia_oficina',
            'id_con_cuentas_bancaria',
            'estado_conciliacion',
            'hash_transaccion',
            'created_at',
            'updated_at'
        ];
    }
}