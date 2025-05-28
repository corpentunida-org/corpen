<?php

namespace App\Models\Seguros;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SegDiagnosticos extends Model
{
    use HasFactory;
    protected $table = 'seg_diagnosticos';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'diagnostico',
    ];

    public function reclamacion()
    {
        return $this->belongsTo(SegReclamaciones::class, 'idDiagnostico', 'id');
    }
    
}