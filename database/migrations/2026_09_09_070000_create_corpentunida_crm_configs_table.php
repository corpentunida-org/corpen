<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Credenciales de la API "Corpentunida CRM" (url/client_id/client_secret),
 * guardadas en BD en vez de en .env: así se pueden editar desde un formulario
 * de Admin sin tocar el .env del servidor (que solo puede editar quien tiene
 * acceso al servidor y requeriría reconstruir la config cacheada). Fila única
 * (singleton) — el modelo siempre trabaja con el registro más reciente.
 * client_secret se guarda cifrado (cast 'encrypted' en el modelo, usa APP_KEY).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('corpentunida_crm_configs', function (Blueprint $table) {
            $table->id();
            $table->string('url');
            $table->string('client_id');
            $table->text('client_secret')->nullable();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('corpentunida_crm_configs');
    }
};
