<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Registro de inicio/fin de sesión por usuario, para poder medir tiempo activo real
 * (indicador de uso de la aplicación). No existía ningún registro de login/logout antes de
 * esto, así que esta tabla solo tiene datos desde que se despliega esta migración en adelante.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sesiones_usuario', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamp('login_at');
            // Se va actualizando en cada request autenticado (ver App\Http\Middleware\RegistrarActividadUsuario)
            // para poder estimar cuándo terminó la sesión aunque el usuario nunca cierre sesión explícitamente
            // (cierre de pestaña, expiración, etc.).
            $table->timestamp('last_activity_at');
            $table->timestamp('logout_at')->nullable();
            $table->string('ip', 45)->nullable();
            $table->timestamps();

            $table->index(['user_id', 'login_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sesiones_usuario');
    }
};
