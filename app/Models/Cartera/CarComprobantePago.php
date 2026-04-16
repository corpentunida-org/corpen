<?php

namespace App\Models\Cartera;

use Illuminate\Database\Eloquent\Model;
use App\Models\Contabilidad\ConExtractoTransaccion;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\Maestras\MaeTerceros;
use App\Models\Interacciones\Interaction;

class CarComprobantePago extends Model
{
    protected $table = 'car_comprobantes_pagos';

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
    ];

    protected $casts = [
        'fecha_pago' => 'integer', 
        'monto_pagado' => 'integer',
        'id_interaction' => 'integer',
        'id_user' => 'integer',
    ];

    /**
     * Accessor para obtener la URL temporal de S3 automáticamente.
     */
    public function getUrlArchivoAttribute()
    {
        $nameFile = $this->ruta_archivo;
        $url = '#';

        if (is_array($nameFile) && count($nameFile) > 0) {
            $nameFile = $nameFile[0];
        }

        if ($nameFile) {
            try {
                if (Storage::disk('s3')->exists($nameFile)) {
                    $url = Storage::disk('s3')->temporaryUrl(
                        $nameFile,
                        now()->addMinutes(10)
                    );
                }
            } catch (\Exception $e) {
                \Log::error('Error al conectar con S3: ' . $e->getMessage());
            }
        }

        return $url;
    }

    public function extractoBancario(): BelongsTo
    {
        return $this->belongsTo(ConExtractoTransaccion::class, 'id_transaccion_bancaria', 'id_transaccion');
    }

    public function getNombreArchivoSimpleAttribute()
    {
        if (!$this->ruta_archivo) return 'Sin archivo';
        
        $partes = explode('/', $this->ruta_archivo);
        return end($partes);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user');
    }   

    public function tercero(): BelongsTo
    {
        return $this->belongsTo(MaeTerceros::class, 'cod_ter_MaeTerceros', 'cod_ter');
    }
    public function interaccion(): BelongsTo
    {
        // Un comprobante pertenece a una interacción
        return $this->belongsTo(Interaction::class, 'id_interaction', 'id');
    }
}