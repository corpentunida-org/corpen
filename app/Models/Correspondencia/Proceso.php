<?php

namespace App\Models\Correspondencia;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;

class Proceso extends Model
{
    use HasFactory;

    protected $table = 'corr_procesos';

    protected $fillable = [
        'id',
        'flujo_id',
        'nombre', // Asegúrate de que 'nombre' esté aquí si lo usas en la vista
        'detalle',
        'activo',
        'numero_archivos', // <-- AGREGADO
        'tipos_archivos',  // <-- AGREGADO
        'usuario_creador_id',
        'created_at',
        'updated_at',
    ];

    /**
     * Casteo de atributos.
     */
    protected $casts = [
        'tipos_archivos' => 'array',
    ];

    /**
     * Relación: pertenece a un flujo de trabajo
     */
    public function flujo()
    {
        return $this->belongsTo(FlujoDeTrabajo::class, 'flujo_id');
    }

    /**
     * Relación: usuario creador del proceso
     */
    public function creador()
    {
        return $this->belongsTo(User::class, 'usuario_creador_id');
    }

    /**
     * Relación Many-to-Many: Acceso directo a los modelos User
     */
    public function usuarios()
    {
        return $this->belongsToMany(User::class, 'corr_procesos_users', 'proceso_id', 'user_id')
                    ->withPivot('detalle')
                    ->withTimestamps();
    }
    
    /**
     * Relación One-to-Many: Acceso a la tabla intermedia como modelo
     * CORRECCIÓN: Se cambió 'id_proceso' por 'proceso_id'
     */
    public function usuariosAsignados()
    {
        return $this->hasMany(ProcesoUsuario::class, 'proceso_id');
    }
    
    /**
     * Relación con los estados/permisos configurados para este proceso.
     */
    public function estadosProcesos()
    {
        // Asegúrate de que los nombres de las columnas coincidan con tu base de datos
        // foreign_key: id_proceso
        return $this->hasMany(EstadoProceso::class, 'id_proceso');
    }
}