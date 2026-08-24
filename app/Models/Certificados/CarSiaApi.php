<?php

namespace App\Models\Certificados;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarSiaApi extends Model
{
    use HasFactory;

    // 1. Especificar el nombre exacto de la tabla
    protected $table = 'car_sia_api';

    // 2. Definir los campos que se pueden llenar masivamente (Mass Assignment)
    protected $fillable = [
        'is_selected',
        'detalle',
        'log_rq',
        'anular',
        'id_factura',
        'cuenta', //ESTE ES EL CAMPO QUE SE USA PARA RELACIONAR CON EL CAMPO "id_cre_lineas_creditos"
        'nombre_cuenta',
        'tercero_base',
        'tercero',
        'nombre_tercero',
        'tercero_cco',
        'doc_mov',
        'cco',
        'trn',
        'numero_documento',
        'pagare',
        'cuota',
        'anio',
        'mes',
        'fecha_venci',
        'estado',
        'contabilizado',
        'nota',
        'fecha_trn_banco',
        'valor_inicial',
        'valor_pago_ofic',
        'valor',
        'valor_banco',
        'uid_banco',
        'banco',
        'fecha_ad',
        'fecha_edit',
        'tipo',
        'id_cab',
        'id_reg_cab_ref',
        'numero_bloque',
    ];

    // Nota: Como esta tabla es de ingesta cruda (Staging), generalmente no tiene
    // relaciones directas (belongsTo) declaradas aquí para evitar dependencias estrictas,
    // pero si lo necesitas a futuro, puedes agregar la relación con facturas u operaciones.
}
