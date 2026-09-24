<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * "Línea de Obligación" en Interacciones usaba directamente el catálogo de Cartera
 * (cre_lineas_creditos, productos de crédito con tasa/plazo/garantía) — funciona para Cartera,
 * pero Interacciones ya es de varias áreas: Seguros de Vida no tiene "líneas de crédito", tiene
 * sus propias subdivisiones (Vida, Todo Riesgo, etc.), que no tienen nada que ver con productos
 * de crédito. Mismo problema que ya se resolvió para Tipos/Resultados/Próxima Acción: un
 * catálogo propio de Interacciones, por área (roles.area), independiente de cre_lineas_creditos.
 *
 * Los 24 registros de Cartera se copian TAL CUAL, con el MISMO id que tienen en
 * cre_lineas_creditos — interactions.id_linea_de_obligacion ya tiene 18.659 filas guardadas con
 * esos ids; si cambiaran, esas interacciones quedarían apuntando a una línea distinta o
 * inexistente. area='cartera' para todos (siguen siendo del área, no compartidos — Cartera es la
 * única que los usa hoy). El auto_increment se deja continuando desde el id más alto ya usado,
 * para que las líneas nuevas de otras áreas no choquen con estos ids preservados.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('int_lineas', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('area', 60)->nullable();
            $table->timestamps();
        });

        $ahora = now();

        $lineasCartera = DB::table('cre_lineas_creditos')->orderBy('id')->get(['id', 'nombre']);
        foreach ($lineasCartera as $linea) {
            DB::table('int_lineas')->insert([
                'id' => $linea->id,
                'name' => $linea->nombre,
                'area' => 'cartera',
                'created_at' => $ahora,
                'updated_at' => $ahora,
            ]);
        }

        // Deja el auto_increment después del id más alto preservado, para que las líneas nuevas
        // (Seguros y las que sigan) no puedan chocar nunca con un id de Cartera ya usado.
        $maxId = (int) $lineasCartera->max('id');
        DB::statement("ALTER TABLE int_lineas AUTO_INCREMENT = ".($maxId + 1));

        $seguros = ['Póliza Seguro de Vida', 'Póliza Vida Deudor', 'Póliza Hogar', 'Póliza de Salud'];
        foreach ($seguros as $nombre) {
            DB::table('int_lineas')->insert([
                'name' => $nombre, 'area' => 'seguros', 'created_at' => $ahora, 'updated_at' => $ahora,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('int_lineas');
    }
};
