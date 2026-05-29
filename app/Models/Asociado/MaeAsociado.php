<?php

namespace App\Models\Asociado;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Maestras\MaeTerceros; // Importación obligatoria para la sincronización

class MaeAsociado extends Model
{
    use HasFactory;

    /**
     * El nombre de la tabla asociada al modelo en la base de datos.
     *
     * @var string
     */
    protected $table = 'mae_asociados';

    /**
     * Los atributos que son asignables masivamente (Mass Assignment).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        // ---------------------------------------------------------
        // GESTIÓN DE ARCHIVO FÍSICO
        // ---------------------------------------------------------
        'radicado',
        
        // ---------------------------------------------------------
        // DATOS DE IDENTIDAD Y DEMOGRÁFICOS
        // ---------------------------------------------------------
        'cedula', //en MaeTerceros se llama cod_ter
        'nombre1', //en MaeTerceros se llama nom1
        'nombre2', //en MaeTerceros se llama nom2
        'apellido1', //en MaeTerceros se llama apl1
        'apellido2',  //en MaeTerceros se llama apl2
        'fecha_nacimiento', //en MaeTerceros se llama fec_nac
        'lugar_expedicion_cedula', //en MaeTerceros se llama lugar_expcc
        'fecha_expedicion', //en MaeTerceros se llama fec_expcc   
        'estado_civil', //en MaeTerceros se llama est_civil
        
        // ---------------------------------------------------------
        // DATOS DE CONTACTO
        // ---------------------------------------------------------
        'correo_pastor', //en MaeTerceros se llama email
        'celular_pastor', //en MaeTerceros se llama tel 
        'whatsapp', //en MaeTerceros se llama cel 
        
        // ---------------------------------------------------------
        // INFORMACIÓN MINISTERIAL Y CORPORATIVA
        // ---------------------------------------------------------
        'fecha_afiliacion', //en MaeTerceros se llama fec_afiliacion
        'distrito_actual', //en MaeTerceros se llama cod_dist
        'ciudad_distrito', 
        'direccion_distrito', 
        'estado_pastor',
        'especificacion',
        'licencia',
        'pais',
        'iglesia_actual', //en MaeTerceros se llama congrega
        
        // ---------------------------------------------------------
        // INFORMACIÓN FAMILIAR (CÓNYUGE)
        // ---------------------------------------------------------
        'cedula_esposa', //en MaeTerceros se llama id_conyuge
        'nombre_esposa', //en MaeTerceros se llama nom_conyug
        'correo_esposa', //en MaeTerceros se llama mail_conyu
        'celular_esposa', //en MaeTerceros se llama tel1
        
        // ---------------------------------------------------------
        // SOPORTES DOCUMENTALES (ANEXOS)
        // ---------------------------------------------------------
        'doc_formulario_afiliacion',
        'doc_autorizacion_datos',
        'doc_cedula_pastor',
        'doc_cedula_esposa',
        'doc_licencia_pastoral',
        'doc_registro_matrimonio',
        'doc_id_hijos',
        
        // ---------------------------------------------------------
        // GESTIÓN DE ARCHIVO DIGITAL (ECM)
        // ---------------------------------------------------------
        'escaneado',
        'cargado_ecm',
        'ubicacion_ecm_link',
        'validado_archivo',
        
        // ---------------------------------------------------------
        // GESTIÓN DE ARCHIVO FÍSICO (COMPLEMENTO)
        // ---------------------------------------------------------
        'ubicacion_carpeta',
        'numero_caja',
        'cantidad_folios',
        'fecha_ingreso_archivo',
        'estado_conservacion',
        'custodia_actual',
        'observaciones_archivo',
        
        // ---------------------------------------------------------
        // METADATOS Y AUDITORÍA
        // ---------------------------------------------------------
        'observaciones_generales',
        'estado', 
    ];

    /**
     * Los atributos que deben ser convertidos (casteados) a tipos nativos.
     * Esto le dice a Laravel cómo tratar estos datos al sacarlos de la DB.
     *
     * @var array<string, string>
     */
    protected $casts = [
        // Casteo a fechas (automáticamente se instancian como Carbon)
        'fecha_nacimiento'      => 'date',
        'fecha_expedicion'      => 'date',
        'fecha_afiliacion'      => 'date',
        'fecha_ingreso_archivo' => 'date',
        
        // Casteo a booleanos lógicos (true/false en vez de 1/0)
        'escaneado'             => 'boolean',
        'cargado_ecm'           => 'boolean',
        'validado_archivo'      => 'boolean',
        
        // Casteo a enteros
        'cantidad_folios'       => 'integer',
    ];

    /**
     * ====================================================================
     * EVENTOS DEL MODELO (Sincronización con MaeTerceros)
     * ====================================================================
     * * El método booted se ejecuta cuando el modelo arranca.
     * Aquí le decimos a Laravel que escuche el evento 'saved' (que ocurre
     * tanto al CREAR como al ACTUALIZAR un registro).
     */
    protected static function booted()
    {
        static::saved(function ($asociado) {
            // VERIFICACIÓN: Si se activó la bandera para omitir la sincronización, detenemos la ejecución.
            if (isset($asociado->skipTerceroSync) && $asociado->skipTerceroSync === true) {
                return;
            }

            $tercero = MaeTerceros::where('cod_ter', $asociado->cedula)->first() ?? new MaeTerceros();

            // ---------------------------------------------------------
            // DATOS DE IDENTIDAD Y DEMOGRÁFICOS
            // ---------------------------------------------------------
            $tercero->cod_ter      = $asociado->cedula;
            $tercero->nom1         = $asociado->nombre1;
            $tercero->nom2         = $asociado->nombre2;
            $tercero->apl1         = $asociado->apellido1;
            $tercero->apl2         = $asociado->apellido2;
            $tercero->nom_ter      = trim("{$asociado->nombre1} {$asociado->nombre2} {$asociado->apellido1} {$asociado->apellido2}");
            $tercero->fec_nac      = $asociado->fecha_nacimiento;
            $tercero->fec_expcc    = $asociado->fecha_expedicion;
            $tercero->lugar_expcc  = $asociado->lugar_expedicion_cedula;
            $tercero->est_civil    = $asociado->estado_civil;
            $tercero->pais         = $asociado->pais ?? 'Colombia';

            // ---------------------------------------------------------
            // DATOS DE CONTACTO
            // ---------------------------------------------------------
            $tercero->email        = $asociado->correo_pastor;
            $tercero->tel          = $asociado->celular_pastor; // Mapeado a 'tel'
            $tercero->cel          = $asociado->whatsapp;       // Mapeado a 'cel'

            // ---------------------------------------------------------
            // INFORMACIÓN MINISTERIAL Y CORPORATIVA
            // ---------------------------------------------------------
            // Nota: En tu modelo MaeTerceros el campo de fecha de ingreso ministerial se llama fec_minis
            $tercero->fec_minis    = $asociado->fecha_afiliacion; 
            $tercero->cod_dist     = $asociado->distrito_actual;  // Mapeado a 'cod_dist'
            $tercero->congrega     = $asociado->iglesia_actual;   // Mapeado a 'congrega'

            // ---------------------------------------------------------
            // INFORMACIÓN FAMILIAR (CÓNYUGE)
            // ---------------------------------------------------------
            $tercero->id_conyuge   = $asociado->cedula_esposa;
            $tercero->nom_conyug   = $asociado->nombre_esposa;
            $tercero->mail_conyu   = $asociado->correo_esposa;
            $tercero->tel1         = $asociado->celular_esposa;   // Mapeado a 'tel1'

            // Valores por defecto si se está creando un tercero nuevo
            if (!$tercero->exists) {
                $tercero->tdoc     = '13'; // Cédula por defecto
                $tercero->tip_pers = 'N';  // Persona natural
                $tercero->tipo_ter = 'A';  // Tipo Asociado
            }

            $tercero->saveQuietly();
        });
    }
    /**
     * ====================================================================
     * RELACIONES DEL MODELO
     * ====================================================================
     */

    /**
     * Relación 1 a 1 bidireccional: Un Asociado pertenece a un único Tercero.
     * * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function tercero()
    {
        // belongsTo(Modelo_Relacionado, 'llave_foranea_local', 'llave_primaria_del_otro_modelo')
        return $this->belongsTo(MaeTerceros::class, 'cedula', 'cod_ter');
    }

    /**
     * ====================================================================
     * ACCESORES Y MUTADORES (Virtual Attributes)
     * ====================================================================
     */

    /**
     * Accessor: Permite obtener el nombre completo del asociado fácilmente en las vistas.
     * Uso en código: {{ $asociado->nombre_completo }}
     *
     * @return string
     */
    public function getNombreCompletoAttribute()
    {
        // trim() asegura que si no hay segundo nombre o apellido, no queden espacios dobles molestos
        return trim("{$this->nombre1} {$this->nombre2} {$this->apellido1} {$this->apellido2}");
    }
}