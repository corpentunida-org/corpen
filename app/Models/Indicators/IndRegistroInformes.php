<?php

namespace App\Models\Indicators;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class IndRegistroInformes extends Model
{
    use HasFactory;

    protected $table = 'Ind_RegistrosInformes'; 
    protected $fillable = [
        'archivo',
        'usuario',
        'fecha_descarga',
    ];

    public function getFile($nameFile)
    {
        $url = '#';
        if ($nameFile && Storage::disk('s3')->exists($nameFile)) {
            $url = Storage::disk('s3')->temporaryUrl(
                $nameFile,
                now()->addMinutes(5)
            );
        }
        return $url;
    }
}
