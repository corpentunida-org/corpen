<?php

namespace App\Models\Inventario;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use Illuminate\Support\Facades\Storage; // <-- Importación obligatoria para que S3 funcione

class InvMantenimiento extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'inv_mantenimientos';

    protected $fillable = [
        'detalle', 'costo_mantenimiento', 'acta',
        'id_InvActivos', 'id_usersRegistro'
    ];

    public function activo() 
    { 
        return $this->belongsTo(InvActivo::class, 'id_InvActivos'); 
    }
    
    public function creador() 
    { 
        return $this->belongsTo(User::class, 'id_usersRegistro'); 
    }

    public function movimientos() 
    { 
        return $this->hasMany(InvMovimiento::class, 'id_mantenimiento'); 
    }

    /**
     * Genera una URL temporal de AWS S3 para ver el documento
     */
    public function getFile($nameFile)
    {
        $url = '#';
        if ($nameFile) {
            if (Storage::disk('s3')->exists($nameFile)) {
                // Genera un link seguro que caduca en 5 minutos
                $url = Storage::disk('s3')->temporaryUrl($nameFile, now()->addMinutes(5));
            }
        }
        return $url;
    }
}