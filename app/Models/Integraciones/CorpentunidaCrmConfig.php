<?php

namespace App\Models\Integraciones;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    protected $casts = [
        // Cifrado en reposo con APP_KEY — nunca se guarda ni se muestra en texto plano.
        'client_secret' => 'encrypted',
    ];

    protected $hidden = [
        'client_secret',
    ];

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
