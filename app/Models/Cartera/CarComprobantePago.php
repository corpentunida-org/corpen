<?php

namespace App\Models\Cartera;

use Illuminate\Database\Eloquent\Model;
use App\Models\Contabilidad\ConExtractoTransaccion;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\Maestras\MaeTerceros;
use App\Models\Interacciones\Interaction;
use App\Models\Creditos\LineaCredito;
use App\Models\Contabilidad\ConCuentaBancaria;

/**
 * Class CarComprobantePago
 *
 * Gestiona los comprobantes de pago de los clientes (terceros).
 * Almacena referencias a transacciones bancarias, el archivo físico en AWS S3
 * y las relaciones con la deuda (obligación) y el usuario que registró el pago.
 */
class CarComprobantePago extends Model
{
    /**
     * Nombre de la tabla asociada en la base de datos.
     * @var string
     */
    protected $table = 'car_comprobantes_pagos';

    /**
     * Atributos que son asignables en masa (Mass Assignment).
     * @var array
     */
    protected $fillable = [
        'cod_ter_MaeTerceros',
        'monto_pagado',
        'fecha_pago',
        'hash_transaccion',
        'ruta_archivo',
        'id_transaccion_bancaria',
        'id_interaction',
        'temp_token',
        'id_user',
        'estado',
        'id_banco',
        'id_obligacion',
        'pr',
        'cco',
        'numero_cuota',
        'tipo_pago',
        'observacion'
    ];

    /**
     * Mutadores de tipo de datos (Casting).
     * Transforma automáticamente los valores al leerlos o guardarlos en la BD.
     * @var array
     */
    protected $casts = [
        'fecha_pago'              => 'integer', // Se maneja como entero (Ej: 20231025143000)
        'monto_pagado'            => 'decimal:2',
        'id_interaction'          => 'integer',
        'id_user'                 => 'integer',
        'id_obligacion'           => 'integer',
        'pr'                      => 'integer',
        'cco'                     => 'integer',
        'numero_cuota'            => 'integer',
        'id_transaccion_bancaria' => 'array', // Transforma el JSON de la BD a Array en PHP
    ];

    /**
     * ACCESSOR: Obtiene la URL temporal del archivo alojado en AWS S3.
     * Se accede en la vista llamando a `$comprobante->url_archivo`.
     *
     * ¡ADVERTENCIA DE RENDIMIENTO (MANTENIMIENTO)!
     * NUNCA usar `Storage::disk('s3')->exists($nameFile)` dentro de este método.
     * Hacerlo provocará que Laravel haga una petición HTTP a S3 por CADA FILA
     * de la tabla (Problema N+1), colapsando el tiempo de carga del sistema.
     * La función `temporaryUrl` se calcula localmente mediante criptografía y es instantánea.
     *
     * @return string
     */
    public function getUrlArchivoAttribute()
    {
        // 1. Extraer el nombre del archivo (si es un array, tomamos el primero)
        $nameFile = is_array($this->ruta_archivo)
                    ? ($this->ruta_archivo[0] ?? null)
                    : $this->ruta_archivo;

        $url = '#';

        // 2. Si hay un nombre de archivo registrado, generamos la firma de S3
        if ($nameFile) {
            try {
                // Genera una URL válida por 10 minutos sin consultar internet
                $url = Storage::disk('s3')->temporaryUrl(
                    $nameFile,
                    now()->addMinutes(10)
                );
            } catch (\Exception $e) {
                // Si las credenciales de S3 fallan, registramos el error silenciosamente
                \Log::error('Error al generar URL S3 en CarComprobantePago: ' . $e->getMessage());
            }
        }

        return $url;
    }

    /**
     * ACCESSOR: Obtiene solo el nombre del archivo sin toda la ruta de carpetas.
     * Útil para mostrar en la vista al usuario.
     * Se accede llamando a `$comprobante->nombre_archivo_simple`.
     *
     * @return string
     */
    public function getNombreArchivoSimpleAttribute()
    {
        if (!$this->ruta_archivo) {
            return 'Sin archivo';
        }

        // Extraemos la ruta real (manejando si por error se guardó como array)
        $ruta = is_array($this->ruta_archivo) ? $this->ruta_archivo[0] : $this->ruta_archivo;

        // basename() extrae la última parte de una ruta (ej: carpeta/archivo.pdf -> archivo.pdf)
        return basename($ruta);
    }

    /**
     * Obtiene los extractos bancarios asociados a este comprobante.
     * Busca en la tabla 'ConExtractoTransaccion' los registros cuyos IDs
     * coincidan con el array guardado en 'id_transaccion_bancaria'.
     */
    public function extractosBancarios()
    {
        // Resguardo: si es nulo, devuelve un array vacío para evitar errores
        $ids = $this->id_transaccion_bancaria ?? [];

        if (empty($ids)) {
            return collect(); // Retorna colección vacía sin tocar la BD
        }

        return ConExtractoTransaccion::whereIn('id_transaccion', $ids)->get();
    }

    /**
     * MUTATOR: Intercepta el guardado de 'id_transaccion_bancaria' antes de ir a la BD.
     * Previene errores si el Frontend envía el texto literal "null" o nulos reales,
     * forzando a que siempre se guarde un JSON válido (por defecto un array vacío '[]').
     *
     * @param mixed $value
     */
    public function setIdTransaccionBancariaAttribute($value)
    {
        $this->attributes['id_transaccion_bancaria'] = is_null($value) || $value === 'null'
            ? json_encode([])
            : (is_string($value) ? $value : json_encode($value));
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS PARA RELACIONES VIRTUALES
    |--------------------------------------------------------------------------
    */

    /**
     * Accessor: Formatea el ID 'pr' para que coincida con el 'doc_mov' de SIA.
     * Ej: 9594 -> "PR-9594"
     */
    public function getPrFormatAttribute()
    {
        return $this->pr ? 'PR-' . $this->pr : null;
    }

    /*
    |--------------------------------------------------------------------------
    | RELACIONES DE ELOQUENT (RELATIONSHIPS)
    |--------------------------------------------------------------------------
    */

    /**
     * Relación: Un comprobante pertenece a un Usuario (el agente que lo registró).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    /**
     * Relación: Un comprobante pertenece a un Tercero (El cliente).
     */
    public function tercero(): BelongsTo
    {
        return $this->belongsTo(MaeTerceros::class, 'cod_ter_MaeTerceros', 'cod_ter');
    }

    /**
     * Relación: Un comprobante puede estar enlazado a un registro de Interacción.
     */
    public function interaccion(): BelongsTo
    {
        return $this->belongsTo(Interaction::class, 'id_interaction', 'id');
    }

    /**
     * Relación: Un comprobante se abona a una Obligación (Línea de Crédito específica).
     */
    public function obligacion(): BelongsTo
    {
        return $this->belongsTo(LineaCredito::class, 'id_obligacion', 'id');
    }

    /**
     * Relación: El comprobante está destinado a una Cuenta Bancaria de la empresa.
     */
    public function banco(): BelongsTo
    {
        return $this->belongsTo(ConCuentaBancaria::class, 'id_banco', 'id');
    }

    /*
    |--------------------------------------------------------------------------
    | RELACIONES Y ACCESSORS (FILTRADO COMPUESTO)
    |--------------------------------------------------------------------------
    */

    /**
     * 1. RELACIÓN BASE: Trae TODOS los registros de la API SIA con este PR
     */
    public function siaApiBase()
    {
        return $this->hasMany(\App\Models\Certificados\CarSiaApi::class, 'doc_mov', 'pr_format');
    }

    /**
     * 2. ACCESSOR VIRTUAL: Encuentra el registro exacto donde la cuota también coincida
     */
    public function getSiaApiAttribute()
    {
        if (!$this->numero_cuota) {
            return null;
        }

        // Casteamos a (string) para que el INT 15 cruce con el VARCHAR "15"
        return $this->siaApiBase->firstWhere('cuota', (string) $this->numero_cuota);
    }
}
