<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * interactions tenía, en 4 columnas, DOS índices idénticos cada una (agent_id, client_id,
     * interaction_date, outcome): uno de la migración original
     * (2025_08_29_084449_create_interactions_table, nombre por defecto de Laravel) y otro con
     * nombre corto (idx_*) agregado después a mano, sin darse cuenta de que ya existía. No
     * aportan nada — un índice duplicado nunca lo usa el optimizador dos veces — y sí penalizan
     * cada escritura (18k+ filas y creciendo). Se elimina el duplicado corto en cada caso.
     *
     * id_user_asignacion, en cambio, no tenía ningún índice pese a usarse en el filtro de
     * seguridad por agente (agent_id = X OR id_user_asignacion = X) de index()/report()/
     * reportPdf(), en las pestañas Vencidos/Próximos y en searchUsers().
     */
    public function up(): void
    {
        Schema::table('interactions', function (Blueprint $table) {
            $table->dropIndex('idx_agent_id');
            $table->dropIndex('idx_client_id');
            $table->dropIndex('idx_interaction_date');
            $table->dropIndex('idx_outcome');
            $table->index('id_user_asignacion');
        });
    }

    public function down(): void
    {
        Schema::table('interactions', function (Blueprint $table) {
            $table->dropIndex(['id_user_asignacion']);
            $table->index('agent_id', 'idx_agent_id');
            $table->index('client_id', 'idx_client_id');
            $table->index('interaction_date', 'idx_interaction_date');
            $table->index('outcome', 'idx_outcome');
        });
    }
};
