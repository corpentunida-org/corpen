<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('rsv_tipo_inmueble', function (Blueprint $table) {
            $table->id(); // Crea un BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
            $table->string('nombre', 255);
            $table->text('descripcion')->nullable();
            $table->boolean('active')->default(true); // Equivale a tinyint(1)
            $table->timestamps(); // Crea automáticamente created_at y updated_at
        });

        // Opcional: Si necesitas agregar la relación a la tabla del catálogo en esta misma migración
        /*
        Schema::table('rsv_catalogo_inmueble', function (Blueprint $table) {
            $table->foreign('tipo_inmueble_id')->references('id')->on('rsv_tipo_inmueble');
        });
        */
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Si activaste la llave foránea arriba, debes eliminarla primero aquí
        /*
        Schema::table('rsv_catalogo_inmueble', function (Blueprint $table) {
            $table->dropForeign(['tipo_inmueble_id']);
        });
        */

        Schema::dropIfExists('rsv_tipo_inmueble');
    }
};
