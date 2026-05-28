<?php

namespace App\Models\Asociado;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaeAsociado extends Model
{
    use HasFactory;

    /**
     * El nombre de la tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'mae_asociados';

    /**
     * Los atributos que son asignables masivamente.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        // Gestión de Archivo Físico
        'radicado',
        
        // Datos de Identidad y Demográficos
        'cedula',
        'nombre1',
        'nombre2',
        'apellido1',
        'apellido2',
        'fecha_nacimiento',
        'lugar_expedicion_cedula',
        'fecha_expedicion',
        'estado_civil',
        
        // Datos de Contacto
        'correo_pastor',
        'celular_pastor',
        'whatsapp',
        
        // Información Ministerial y Corporativa
        'fecha_afiliacion',
        'distrito_actual',
        'ciudad_distrito',
        'direccion_distrito',
        'estado_pastor',
        'especificacion',
        'licencia',
        'pais',
        'iglesia_actual',
        
        // Información Familiar (Cónyuge)
        'cedula_esposa',
        'nombre_esposa',
        'correo_esposa',
        'celular_esposa',
        
        // Soportes Documentales (Anexos)
        'doc_formulario_afiliacion',
        'doc_autorizacion_datos',
        'doc_cedula_pastor',
        'doc_cedula_esposa',
        'doc_licencia_pastoral',
        'doc_registro_matrimonio',
        'doc_id_hijos',
        
        // Gestión de Archivo Digital (ECM)
        'escaneado',
        'cargado_ecm',
        'ubicacion_ecm_link',
        'validado_archivo',
        
        // Gestión de Archivo Físico (Complemento)
        'ubicacion_carpeta',
        'numero_caja',
        'cantidad_folios',
        'fecha_ingreso_archivo',
        'estado_conservacion',
        'custodia_actual',
        'observaciones_archivo',
        
        // Metadatos y Auditoría
        'observaciones_generales',
        'estado', 
    ];

    /**
     * Los atributos que deben ser convertidos (casteados) a tipos nativos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        // Casteo a fechas (automáticamente se instancian como Carbon)
        'fecha_nacimiento'      => 'date',
        'fecha_expedicion'      => 'date',
        'fecha_afiliacion'      => 'date',
        'fecha_ingreso_archivo' => 'date',
        
        // Casteo a booleanos lógicos (true/false)
        'escaneado'             => 'boolean',
        'cargado_ecm'           => 'boolean',
        'validado_archivo'      => 'boolean',
        
        // Casteo a enteros
        'cantidad_folios'       => 'integer',
    ];

    /**
     * Accessor: Obtener el nombre completo del asociado fácilmente.
     * Uso en código: $asociado->nombre_completo
     *
     * @return string
     */
    public function getNombreCompletoAttribute()
    {
        return trim("{$this->nombre1} {$this->nombre2} {$this->apellido1} {$this->apellido2}");
    }
}