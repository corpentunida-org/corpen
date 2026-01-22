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
        Schema::create('inv_detalle_compras', function (Blueprint $table) {
            $table->id(); // <--- Verifica este ;
            
            $table->integer('cantidades');
            $table->decimal('precio_unitario', 15, 2);
            $table->decimal('sub_total', 15, 2);
            
            // Relación con la Compra
            $table->foreignId('id_InvCompras')
                ->constrained('inv_compras')
                ->onDelete('cascade');
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inv_detalle_compras');
    }
};
