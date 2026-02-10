<?php

namespace App\Models\Correspondencia;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;

class Trd extends Model
{
    use HasFactory;

    protected $table = 'corr_trd';

    protected $primaryKey = 'id_trd';

    protected $fillable = [
        'serie_documental',
        'tiempo_gestion',
        'tiempo_central',
        'disposicion_final',
        'usuario_id',
        'fk_flujo',
    ];

    /**
     * Relación: usuario creador/responsable
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    /**
     * Relación: flujo de trabajo asociado
     */
    public function flujo()
    {
        // Se asocia con el modelo de Flujo de Trabajo usando la nueva columna
        return $this->belongsTo(FlujoDeTrabajo::class, 'fk_flujo');
    }
}