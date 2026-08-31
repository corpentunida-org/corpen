<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('car_sia_operaciones_lineas', function (Blueprint $table) {
            // 1. Auditoría: Usuario (Nullable según tu imagen)
            $table->foreignId('id_user')
                  ->nullable()
                  ->after('deleted_at')
                  ->constrained('users'); // Asumiendo que tu tabla de usuarios se llama 'users'

            // 2. Auditoría: Tipo (Puesto nullable por seguridad de los 24k registros existentes)
            $table->foreignId('id_car_sia_tipos')
                  ->nullable()
                  ->after('id_user')
                  ->constrained('car_sia_tipos'); // Ajusta el nombre de la tabla si es distinto

            // 3. Hash del Certificado
            $table->string('hash_certificado', 255)
                  ->nullable()
                  ->after('id_car_sia_tipos');
        });
    }

    public function down(): void
    {
        Schema::table('car_sia_operaciones_lineas', function (Blueprint $table) {
            // Primero se eliminan las llaves foráneas, luego las columnas
            $table->dropForeign(['id_user']);
            $table->dropForeign(['id_car_sia_tipos']);
            $table->dropColumn(['id_user', 'id_car_sia_tipos', 'hash_certificado']);
        });
    }
};
