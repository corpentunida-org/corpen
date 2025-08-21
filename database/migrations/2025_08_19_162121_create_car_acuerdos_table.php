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
        Schema::create('car_acuerdos', function (Blueprint $table) {
            // PK: id: AI
            $table->id();

            // --- FK: Clave Foránea Principal ---
            // Conecta el acuerdo con el crédito original. Se permite que un crédito tenga
            // un historial de acuerdos (ej. uno incumplido y luego uno nuevo).
            $table->foreignId('cre_creditos_id')
                  ->constrained('cre_creditos')
                  ->onDelete('cascade');

            // --- 1. Información General del Acuerdo ---
            $table->string('numero_acuerdo')->unique()->comment('Un código o folio único para identificar el acuerdo.');
            $table->date('fecha_acuerdo')->comment('Fecha en que se pactó el acuerdo.');
            $table->enum('estado', ['activo', 'incumplido', 'pagado', 'cancelado'])->default('activo');
            $table->integer('dias_mora_inicial')->comment('Cuántos días de mora tenía el cliente al momento del acuerdo.');

            // --- 2. Detalles Financieros Congelados al Momento del Acuerdo ---

            $table->decimal('intereses_corrientes_acuerdo', 15, 2)->comment('Intereses normales acumulados.');
            $table->decimal('intereses_mora_acuerdo', 15, 2)->comment('Intereses por mora acumulados.');
            $table->decimal('gastos_cobranza', 15, 2)->nullable()->comment('Gastos legales o de cobranza incluidos.');


            // --- 3. Nuevas Condiciones de Pago ---

            // --- 4. Auditoría y Seguimiento ---
            $table->foreignId('user_id')->nullable()->constrained('users')->comment('El usuario (agente) que gestionó el acuerdo.');
            $table->text('observaciones')->nullable()->comment('Notas adicionales, condiciones especiales, etc.');

            // Timestamps (created_at y updated_at) para saber cuándo se creó o modificó el acuerdo.
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
        Schema::dropIfExists('car_acuerdos');
    }
};