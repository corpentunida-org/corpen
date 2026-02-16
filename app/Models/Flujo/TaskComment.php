<?php

namespace App\Models\Flujo;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class TaskComment extends Model
{
    use HasFactory;
    protected $table = 'wor_task_comments';

    public function getFile ($nameFile)
    {
        $url = '#';
        if($nameFile) {
            if (Storage::disk('s3')->exists($nameFile)) {
                $url = Storage::disk('s3')->temporaryUrl(
                    $nameFile, now()->addMinutes(5)
                );
            }
        }
        return $url;
    }

    protected $fillable = [
        'task_id',
        'user_id',
        'comentario',
        'soporte' // Ruta del archivo en AWS S3
    ];

    /**
     * Conversión de tipos.
     * Garantiza que created_at sea un objeto Carbon para usar format() o diffForHumans().
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Tarea a la que pertenece el comentario (Relación Inversa)
     */
    public function task() 
    {
        return $this->belongsTo(Task::class);
    }

    /**
     * Usuario que registró el feedback
     */
    public function user() 
    {
        return $this->belongsTo(User::class);
    }

    /**
     * URL del archivo de soporte en AWS S3
     * Uso: $comment->soporte_url
     */
    public function getSoporteUrlAttribute()
    {
        if (!$this->soporte) {
            return null;
        }

        // Si el bucket es privado (recomendado), usa temporaryUrl para links firmados que expiran
        return Storage::disk('s3')->temporaryUrl(
            $this->soporte, 
            now()->addMinutes(15)
        );
        
        // Si el bucket es público, podrías usar:
        // return Storage::disk('s3')->url($this->soporte);
    }

    /**
     * Determina si el soporte es una imagen
     * Uso: $comment->es_imagen
     */
    public function getEsImagenAttribute()
    {
        if (!$this->soporte) return false;
        $extension = pathinfo($this->soporte, PATHINFO_EXTENSION);
        return in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
    }
}