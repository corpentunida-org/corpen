<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scp_categorias', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 150);
            $table->text('descripcion')->nullable();
            $table->timestamps();
        });

        // Ahora agregamos la FK en scp_tipos (si aún no está)
        Schema::table('scp_tipos', function (Blueprint $table) {
            $table->unsignedBigInteger('id_categoria')->nullable()->after('descripcion');
            $table->foreign('id_categoria')
                  ->references('id')
                  ->on('scp_categorias')
                  ->onDelete('set null'); 
        });
    }

    public function down(): void
    {
        Schema::table('scp_tipos', function (Blueprint $table) {
            $table->dropForeign(['id_categoria']);
            $table->dropColumn('id_categoria');
        });

        Schema::dropIfExists('scp_categorias');
    }
};
