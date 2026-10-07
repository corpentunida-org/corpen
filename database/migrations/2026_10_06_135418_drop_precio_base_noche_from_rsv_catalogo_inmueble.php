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
        Schema::table('rsv_catalogo_inmueble', function (Blueprint $table) {
            $table->dropColumn('precio_base_noche');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rsv_catalogo_inmueble', function (Blueprint $table) {
            $table->decimal('precio_base_noche', 10, 2); // Por si necesitas hacer rollback en el futuro
        });
    }
};
