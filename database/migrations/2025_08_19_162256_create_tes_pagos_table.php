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
        Schema::create('tes_pagos', function (Blueprint $table) {
            // PK: Id: AI
            $table->id();

            // Atributo: valor_pagado - Usamos DECIMAL por precisión
            $table->decimal('valor_pagado', 15, 2);

            // FK: Clave Foránea que conecta con el crédito
            $table->foreignId('cre_creditos_id')
                  ->constrained('cre_creditos')
                  ->onDelete('restrict'); // También protegemos contra borrado

            // Timestamps para saber la fecha de registro en tesorería
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
        Schema::dropIfExists('tes_pagos');
    }
};