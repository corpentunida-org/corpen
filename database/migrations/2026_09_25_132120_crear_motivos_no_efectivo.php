<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Catálogo de motivos de "No Efectivo" — mismo patrón área-scoped que Tipos/Resultados/
     * Próxima Acción/Líneas (área nullable = compartido para todas las áreas). Se siembra con la
     * lista pedida como compartida (area=null): son razones de contacto fallido que aplican en
     * general, no exclusivas de una sola área — si alguna área necesita agregar una propia más
     * adelante, puede hacerlo desde la pantalla (auto-etiquetada a su área, mismo criterio que
     * los demás catálogos).
     */
    public function up(): void
    {
        Schema::create('int_motivos_no_efectivo', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('area')->nullable();
            $table->timestamps();
        });

        $motivos = [
            'Número equivocado',
            'Sin tono',
            'Buzón de voz',
            'Apagado',
            'Tercero no coopera',
            'Tercero sin recado',
            'Negativa de pago',
            'Promesa rota',
            'Llamada colgada',
            'Disputa de deuda',
            'Incapacidad de pago',
            'Mensaje ignorado',
            'Correo rebotado',
            'Sin respuesta',
        ];

        $ahora = now();
        DB::table('int_motivos_no_efectivo')->insert(
            array_map(fn ($nombre) => [
                'name' => $nombre,
                'area' => null,
                'created_at' => $ahora,
                'updated_at' => $ahora,
            ], $motivos)
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('int_motivos_no_efectivo');
    }
};
