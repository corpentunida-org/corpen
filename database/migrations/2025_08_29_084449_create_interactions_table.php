<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInteractionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('interactions', function (Blueprint $table) {
            $table->id(); // Campo 'id': ID único de la interacción (PK, autoincremental).

            // Campos relacionados con otras entidades (por ahora sin FK explícitas)
            $table->unsignedBigInteger('client_id'); // Campo 'client_id': ID del cliente asociado a esta interacción.
            $table->unsignedBigInteger('agent_id');  // Campo 'agent_id': ID del agente que realizó o gestionó la interacción.

            // Información principal de la interacción
            $table->dateTime('interaction_date'); // Campo 'interaction_date': Fecha y hora en que se realizó la interacción.
            $table->string('interaction_channel'); // Campo 'interaction_channel': Canal de la interacción (e.g., "Teléfono", "WhatsApp Call", "WhatsApp Message", "Email").
            $table->string('interaction_type'); // Campo 'interaction_type': Tipo de interacción (e.g., "Entrante", "Saliente", "Seguimiento", "Cobranza", "Envío de Información").
            $table->integer('duration')->nullable(); // Campo 'duration': Duración de la interacción en minutos o segundos. Puede ser null (para mensajes/emails).
            $table->string('outcome'); // Campo 'outcome': Resultado de la interacción (e.g., "Concretada", "No contesta", "Mensaje Enviado", "Email Enviado", "Compromiso de pago", "Leído").
            $table->text('notes')->nullable(); // Campo 'notes': Notas detalladas de la conversación o del contenido del mensaje/email. Puede ser null.

            // Campo para enlazar interacciones previas (si es un seguimiento)
            $table->unsignedBigInteger('parent_interaction_id')->nullable(); // Campo 'parent_interaction_id': ID de una interacción previa si esta es un seguimiento o respuesta. Puede ser null.

            // Campos para la próxima acción o seguimiento programado
            $table->dateTime('next_action_date')->nullable(); // Campo 'next_action_date': Fecha y hora para la próxima acción o seguimiento programado. Puede ser null.
            $table->string('next_action_type')->nullable(); // Campo 'next_action_type': Tipo de la próxima acción (e.g., "Volver a llamar", "Enviar WhatsApp", "Visita"). Puede ser null.
            $table->text('next_action_notes')->nullable(); // Campo 'next_action_notes': Notas o detalles sobre la próxima acción. Puede ser null.

            // Campo para URLs de evidencias (imágenes/PDFs) almacenadas en Google Drive
            // Si tu base de datos (ej. MySQL < 5.7) no soporta el tipo JSON, cambia esto a $table->text('attachment_urls')->nullable();
            $table->json('attachment_urls')->nullable(); // Campo 'attachment_urls': Almacena un array de URLs (strings) de imágenes/PDFs como JSON. Puede ser null.

            // Campo para URL de la interacción (grabación, enlace a chat, email)
            $table->text('interaction_url')->nullable(); // Campo 'interaction_url': URL directa a la grabación de la llamada, conversación de WhatsApp, o enlace a email en un sistema de tickets. Puede ser null.

            // Marcas de tiempo automáticas de Laravel para seguimiento del registro
            $table->timestamps(); // Campos 'created_at' y 'updated_at': Marcas de tiempo de creación y última actualización del registro.

            // --------------------------------------------------------------------------------
            // Índices para mejorar el rendimiento de las consultas (¡ALTAMENTE RECOMENDADO!)
            // Estos índices acelerarán las búsquedas y filtrados comunes en tu aplicación.
            // --------------------------------------------------------------------------------
            $table->index('client_id');          // Acelera consultas que filtren por el ID del cliente.
            $table->index('agent_id');           // Acelera consultas que filtren por el ID del agente.
            $table->index('interaction_date');   // Acelera consultas que filtren o ordenen por la fecha de la interacción.
            $table->index('interaction_channel'); // Acelera consultas que filtren por el canal de la interacción.
            $table->index('interaction_type');   // Acelera consultas que filtren por el tipo de interacción.
            $table->index('outcome');            // Acelera consultas que filtren por el resultado de la interacción.
            // Considera añadir más índices si identificas otras columnas con búsquedas frecuentes.
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('interactions');
    }
}
