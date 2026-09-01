<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('car_sia_periodos', function (Blueprint $table) {
            $table->id();

            $table->integer('anio')->comment('Año del periodo contable/operativo (ej. 2026)');
            $table->tinyInteger('mes')->comment('Mes numérico (1-12)');
            $table->string('nombre', 50)->comment('Etiqueta para vistas (ej. Agosto 2026)');

            $table->boolean('abierto')->default(true)->comment('Controla si se pueden inyectar nuevos lotes a este mes');

            $table->timestamps();

            // Restricción a nivel de base de datos
            $table->unique(['anio', 'mes'], 'idx_periodo_unico');
        });
    }

    public function down()
    {
        Schema::dropIfExists('car_sia_periodos');
    }
};
