<?php

namespace App\Models\Seguros;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SegBeneficiario extends Model
{
    use HasFactory;
    protected $table = 'SEG_beneficiarios';
    protected $fillable = [
        'nombre', 
        'parentesco', 
        'porcentaje', 
        'cedula_contingente', 
        'nombre_contingente', 
        'telefono', 
        'correo', 
        'id_asegurado', 
    ];
    
    public function poliza()
    {
        return $this->belongsTo(SegPoliza::class, 'id_poliza', 'id');
    }

    public function asegurado()
    {
        return $this->belongsTo(SegAsegurado::class, 'id_asegurado', 'cedula');
    }

    public function parentescos()
    {
        return $this->belongsTo(Parentescos::class, 'parentesco', 'code');
    }
}
