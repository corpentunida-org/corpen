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
        'cuenta', //ESTE ES EL CAMPO QUE SE USA PARA RELACIONAR CON EL CAMPO "id_car_sia_lineas" DE LA TABLA "car_sia_operaciones_lineas"
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

    public function lineaSia()
    {
        return $this->belongsTo(CarSiaLinea::class, 'cuenta', 'cuenta');
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS PARA RELACIONES VIRTUALES
    |--------------------------------------------------------------------------
    */

    /**
     * Accessor: Extrae solo el número del 'doc_mov'.
     * Ej: "PR-9594" -> 9594
     */
    public function getPrNumberAttribute()
    {
        if (!$this->doc_mov) {
            return null;
        }

        // str_replace es muy rápido. Si a futuro tienes otros prefijos como "NC-9594",
        // puedes usar preg_replace('/[^0-9]/', '', $this->doc_mov) para dejar solo los números.
        return (int) str_replace('PR-', '', $this->doc_mov);
    }

    /*
    |--------------------------------------------------------------------------
    | RELACIONES Y ACCESSORS (FILTRADO COMPUESTO)
    |--------------------------------------------------------------------------
    */

    /**
     * 1. RELACIÓN BASE: Trae TODOS los comprobantes que coincidan con el PR.
     * Usamos 'hasMany' porque un PR puede tener varias cuotas (Ej: 1, 2, 3...)
     */
    public function comprobantesBase()
    {
        return $this->hasMany(\App\Models\Cartera\CarComprobantePago::class, 'pr', 'pr_number');
    }

    /**
     * 2. ACCESSOR VIRTUAL: Filtra la colección en memoria buscando la cuota exacta.
     * Como se llama 'getComprobantePagoAttribute', en Blade se usará como ->comprobantePago
     */
    public function getComprobantePagoAttribute()
    {
        // Si no hay cuota o no es un número, no hay coincidencia válida
        if (!$this->cuota || !is_numeric($this->cuota)) {
            return null;
        }

        // Buscamos dentro de los comprobantes cargados el que tenga el mismo número de cuota.
        // Casteamos (int) para que el "15" (VARCHAR) se convierta en 15 (INT) y crucen perfecto.
        return $this->comprobantesBase->firstWhere('numero_cuota', (int) $this->cuota);
    }
    /*
    |--------------------------------------------------------------------------
    | RELACIONES CON ASOCIADO (CRM / DOCUMENTOS)
    |--------------------------------------------------------------------------
    */

    /**
     * Relación: Trae los documentos y datos del asociado usando el número de cédula.
     */
    public function asociado()
    {
        // belongsTo(Modelo_Relacionado, 'llave_foranea_local', 'llave_primaria_del_otro_modelo')
        return $this->belongsTo(\App\Models\Asociado\MaeAsociado::class, 'tercero', 'cedula');
    }
}
