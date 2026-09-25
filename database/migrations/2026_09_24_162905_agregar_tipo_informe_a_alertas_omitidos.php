<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Tercer tipo de omisión: 'informe' — el agente no sale en la fila del informe semanal por
     * área (correo a los admon), aunque siga contando para "correo" (su propio correo diario de
     * vencidas) y "pantalla" (el modal forzado) de forma independiente. Alterar el ENUM en vez
     * de crear otra tabla, mismo patrón compartido que ya tenían 'correo'/'pantalla'.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE int_alerta_omitidos MODIFY tipo ENUM('correo', 'pantalla', 'informe') NOT NULL");
    }

    public function down(): void
    {
        DB::table('int_alerta_omitidos')->where('tipo', 'informe')->delete();
        DB::statement("ALTER TABLE int_alerta_omitidos MODIFY tipo ENUM('correo', 'pantalla') NOT NULL");
    }
};
