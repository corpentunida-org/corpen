<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Soporte para la nueva pantalla unificada de Gestión de Usuarios (empleados + asociados):
     * - bloqueado: impide el login sin borrar nada (reversible desde la misma pantalla).
     * - debe_cambiar_password: cuando un admin asigna una contraseña, puede marcar que se le
     *   exija cambiarla en el próximo login (ver App\Http\Middleware\ForzarCambioPassword).
     * - deleted_at (SoftDeletes): "eliminar" un usuario no borra la fila — hay más de una
     *   decena de tablas (Auditoria, res_reservas, interactions, scp_soportes, wor_tasks,
     *   UserSesion, model_has_permissions...) que referencian users.id sin llave foránea real;
     *   un borrado físico las dejaría huérfanas. Soft-delete ya excluye al usuario del login y
     *   de los listados por defecto de Eloquent, sin perder el historial.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('bloqueado')->default(false)->after('type');
            $table->boolean('debe_cambiar_password')->default(false)->after('password');
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['bloqueado', 'debe_cambiar_password']);
            $table->dropSoftDeletes();
        });
    }
};
