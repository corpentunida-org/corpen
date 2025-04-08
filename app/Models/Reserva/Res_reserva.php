<?php

namespace App\Models\Reserva;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Res_reserva extends Model
{
    use HasFactory;
    use SoftDeletes;

    public function res_inmueble()
    {
        return $this->belongsTo(Res_inmueble::class);
    }

    public function res_status()
    {
        return $this->belongsTo(Res_status::class);
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

    public function user ()
    {
        return $this->belongsTo(User::class);
    }
}
