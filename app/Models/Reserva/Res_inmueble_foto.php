<?php

namespace App\Models\Reserva;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Reserva\Res_inmueble;

class Res_inmueble_foto extends Model
{
    use HasFactory;
    protected $table = 'res_inmueble_fotos';

    protected $fillable = ['res_inmueble_id', 'attached'];

    public function inmueble()
    {
        return $this->belongsTo(Res_inmueble::class, 'res_inmueble_id');
    }

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
}
