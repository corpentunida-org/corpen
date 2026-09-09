<?php

namespace App\Models\Integraciones;

use App\Models\User;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

class CorpentunidaCrmConfig extends Model
{
    use HasFactory;

    protected $table = 'corpentunida_crm_configs';

    protected $fillable = [
        'url',
        'client_id',
        'client_secret',
        'updated_by',
    ];

    protected $hidden = [
        'client_secret',
    ];

    /**
     * Cifrado en reposo con APP_KEY — nunca se guarda ni se muestra en texto
     * plano. Manejado a mano (en vez del cast 'encrypted') para que una fila
     * cifrada con una APP_KEY vieja (rotación de key, entorno distinto) se
     * trate como "sin configurar" en lugar de tumbar con un DecryptException
     * cualquier pantalla o flujo que solo necesita saber si hay secreto.
     */
    protected function clientSecret(): Attribute
    {
        return Attribute::make(
            get: function (?string $value) {
                if ($value === null) {
                    return null;
                }
                try {
                    return Crypt::decryptString($value);
                } catch (DecryptException $e) {
                    Log::warning('Corpentunida CRM: no se pudo descifrar client_secret guardado (APP_KEY distinta a la que lo cifró). Se trata como sin configurar.');
                    return null;
                }
            },
            set: fn (?string $value) => $value === null ? null : Crypt::encryptString($value),
        );
    }

    public function actualizadoPor()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /** Única fila vigente (singleton) — null si nunca se ha configurado. */
    public static function actual(): ?self
    {
        return static::latest('id')->first();
    }
}
