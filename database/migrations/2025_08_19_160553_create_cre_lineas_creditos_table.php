<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cre_lineas_creditos', function (Blueprint $table) {
            // PK: id: AI (Auto-Increment)
            $table->id();

            // Atributos de la tabla
            $table->string('nombre');
            $table->integer('cuenta');
            
            // Para 'tasa_interes' usamos decimal para mayor precisión
            $table->decimal('tasa_interes', 8, 2); // 8 dígitos en total, 2 decimales

            $table->integer('plazo_minimo');
            $table->integer('plazo_maximo');
            $table->integer('edad_minima');
            $table->integer('edad_maxima');

            // Nuevas columnas solicitadas
            $table->integer('monto_maximo');
            $table->integer('monto_minimo');
            $table->integer('seguro_todo_riesgo');
            
            // 'DATA' se interpreta como tipo 'date'
            $table->date('fecha_apertura');
            $table->date('fecha_cierre')->nullable(); // La hacemos 'nullable' por si la línea aún no ha cerrado

            // 'observacion' como TEXT es mejor para textos largos y lo hacemos opcional
            $table->text('observacion')->nullable();

            // FK: Claves Foráneas
            // Laravel asume que se relaciona con el 'id' de la tabla 'cre_garantias'
            $table->foreignId('cre_garantias_id')->constrained('cre_garantias');

            // Laravel asume que se relaciona con el 'id' de la tabla 'cre_tipos_creditos'
            $table->foreignId('cre_tipos_creditos_id')->constrained('cre_tipos_creditos');
            
            // Timestamps (created_at y updated_at). Es una convención estándar de Laravel.
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cre_lineas_creditos');
    }
};