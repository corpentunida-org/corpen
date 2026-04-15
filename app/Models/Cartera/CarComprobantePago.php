<?php

namespace App\Models\Cartera;

use Illuminate\Database\Eloquent\Model;
use App\Models\Contabilidad\ConExtractoTransaccion;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage; // Importante para S3
use App\Models\User;
use App\Models\Maestras\MaeTerceros;

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
        'id_user',
        'estado', // NUEVO: 'pendiente', 'conciliado', 'rechazado'
    ];

    protected $casts = [
        'fecha_pago' => 'integer', 
        'monto_pagado' => 'integer',
        'id_interaction' => 'integer',
        'id_user' => 'integer',
    ];

    /**
     * Accessor para obtener la URL temporal de S3 automáticamente.
     * En tu vista (Blade) o JSON, simplemente llamas: $comprobante->url_archivo
     */
    public function getUrlArchivoAttribute()
    {
        $nameFile = $this->ruta_archivo;
        $url = '#';

        // Si nameFile llega como un array (por un cast en el modelo), tomamos el primer elemento
        if (is_array($nameFile) && count($nameFile) > 0) {
            $nameFile = $nameFile[0];
        }

        if ($nameFile) {
            try {
                if (Storage::disk('s3')->exists($nameFile)) {
                    $url = Storage::disk('s3')->temporaryUrl(
                        $nameFile,
                        now()->addMinutes(10) // Aumentado a 10 min
                    );
                }
            } catch (\Exception $e) {
                // Previene que la app se caiga si hay un problema temporal con S3
                \Log::error('Error al conectar con S3: ' . $e->getMessage());
            }
        }

        return $url;
    }

    /**
     * Relación opcional con el extracto bancario.
     */
    public function extractoBancario(): BelongsTo
    {
        return $this->belongsTo(ConExtractoTransaccion::class, 'id_transaccion_bancaria', 'id_transaccion');
    }
    /**
     * Obtiene solo el nombre del archivo sin la ruta de carpetas.
     * Útil para mostrar en la interfaz de usuario.
     */
    public function getNombreArchivoSimpleAttribute()
    {
        if (!$this->ruta_archivo) return 'Sin archivo';
        
        $partes = explode('/', $this->ruta_archivo);
        return end($partes);
    }
/**
     * Relación con el modelo User (Agente que registró el comprobante)
     */
    public function user(): BelongsTo
    {
        // Relacionamos la columna 'id_user' de esta tabla con el 'id' del usuario
        return $this->belongsTo(User::class, 'id_user');
    }  
    public function tercero(): BelongsTo
    {
        // belongsTo(Modelo, llave_foranea_local, llave_primaria_tercero)
        return $this->belongsTo(MaeTerceros::class, 'cod_ter_MaeTerceros', 'cod_ter');
    }
}