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
        Schema::create('int_seguimiento', function (Blueprint $table) {
            $table->id(); // bigint UN AI PK
            
            // Llaves principales de la relación
            $table->unsignedBigInteger('id_interaction');
            $table->unsignedBigInteger('agent_id');
            $table->unsignedBigInteger('id_user_asignacion');

            // Tipificaciones y resultados
            $table->unsignedBigInteger('outcome'); 
            $table->unsignedBigInteger('next_action_type')->nullable(); 
            
            // Campos de seguimiento y agenda
            $table->dateTime('next_action_date')->nullable();
            $table->text('next_action_notes')->nullable();

            // Soportes y links
            $table->text('attachment_urls')->nullable(); 
            $table->text('interaction_url')->nullable(); 
            
            // Tiempos de sistema
            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Definición de Llaves Foráneas
            |--------------------------------------------------------------------------
            */
            
            // Relación con la interacción padre (si se borra la interacción, se borra el seguimiento)
            $table->foreign('id_interaction')->references('id')->on('interactions')->onDelete('cascade');
            
            // Relación con usuarios (Agente que registra y Agente asignado)
            $table->foreign('agent_id')->references('id')->on('users');
            $table->foreign('id_user_asignacion')->references('id')->on('users');

            // Las dos relaciones que faltaban:
            $table->foreign('outcome')->references('id')->on('int_outcomes');
            $table->foreign('next_action_type')->references('id')->on('int_next_actions');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('int_seguimiento');
    }
};
