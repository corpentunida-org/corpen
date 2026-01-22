<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('inv_compras', function (Blueprint $table) {
            $table->id();
            
            // Datos de la Factura
            $table->string('numero_factura');
            $table->date('fecha_factura');
            $table->decimal('total_pago', 15, 2); // 15 dígitos, 2 decimales para dinero
            $table->string('num_doc_interno')->nullable();
            $table->integer('numero_egreso')->nullable();
            $table->string('eg_archivo')->nullable(); // Ruta del PDF
            
            // Relaciones
            $table->foreignId('id_InvMetodos')->constrained('inv_metodos');
            $table->foreignId('id_usersRegistro')->constrained('users'); // Usuario que digita
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inv_compras');
    }
};
