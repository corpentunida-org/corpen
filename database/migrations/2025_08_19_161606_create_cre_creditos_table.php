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
        Schema::create('cre_creditos', function (Blueprint $table) {
            // PK: Id: IA (Auto-Increment)
            $table->id();

            // Atributos
            $table->integer('pr')->unique()->comment('Número o identificador del préstamo');
            $table->integer('pagare')->unique()->comment('Número del pagaré asociado');
            $table->decimal('valor', 15, 2);
            $table->integer('cuotas');
            $table->date('fecha_desembolso');


            // FK: Claves Foráneas
            $table->foreignId('cre_estados_id')->constrained('cre_estados');
            $table->foreignId('cre_lineas_creditos_id')->constrained('cre_lineas_creditos');
            
            // ----- INICIO DE LA SOLUCIÓN CON 'cod_ter' COMO PK -----
            
            // 1. Se define la columna que contendrá la referencia.
            //    Debe ser de tipo bigInteger para coincidir con el tipo de 'cod_ter'.
            $table->bigInteger('mae_terceros_cod_ter');

            // 2. Se crea la restricción de clave foránea.
            //    Ahora funcionará porque 'cod_ter' tiene un índice (al ser PK).
            $table->foreign('mae_terceros_cod_ter')
                  ->references('cod_ter')->on('MaeTerceros');
                  
            // ----- FIN DE LA SOLUCIÓN CON 'cod_ter' COMO PK -----
            
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
        Schema::dropIfExists('cre_creditos');
    }
};