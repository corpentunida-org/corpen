<?php

namespace App\Models\Correspondencia;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;

class Proceso extends Model
{
    use HasFactory;

    protected $table = 'corr_procesos';

    /**
     * Campos habilitados para asignación masiva.
     * Se removieron 'id', 'created_at' y 'updated_at' ya que Eloquent los administra automáticamente.
     */
    protected $fillable = [
        'flujo_id',
        'nombre',
        'detalle',
        'activo',
        'numero_archivos',
        'secuencia',
        'tipos_archivos',
        'tiempo_respuesta_dias',
        'usuario_creador_id',
    ];

    /**
     * Casteo de atributos.
     */
    protected $casts = [
        'activo'                => 'boolean', // <-- AGREGADO: Convierte el tinyint a true/false automáticamente
        'tipos_archivos'        => 'array',
        'tiempo_respuesta_dias' => 'integer', // <-- AGREGADO: Garantiza que se trate como entero
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
                    ->withPivot('detalle', 'activo') // <-- AGREGADO 'activo' para que coincida con la lógica de tu vista
                    ->withTimestamps();
    }
    
    /**
     * Relación One-to-Many: Acceso a la tabla intermedia como modelo
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
        return $this->hasMany(EstadoProceso::class, 'id_proceso');
    }
}