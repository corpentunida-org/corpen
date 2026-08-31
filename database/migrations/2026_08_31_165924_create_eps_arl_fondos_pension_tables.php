<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Catálogos para sgrh_empleados.eps/arl/fondo_pension (hoy texto libre, sin datos aún —
 * tabla nueva). Lista razonable de entidades activas en Colombia, no verificada contra una
 * fuente oficial actualizada — el sector EPS/ARL ha tenido fusiones/liquidaciones recientes.
 * `activo` permite desactivar una entidad sin borrar el historial de quién la tenía asignada.
 * El formulario deja elegir "Otra (especificar)" para no depender de que la lista esté
 * completa o vigente.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sgrh_eps', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        Schema::create('sgrh_arl', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        Schema::create('sgrh_fondos_pension', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        $ahora = now();

        DB::table('sgrh_eps')->insert(collect([
            'Nueva EPS', 'EPS Sura', 'EPS Sanitas', 'Compensar EPS', 'Famisanar EPS',
            'Salud Total EPS', 'Coosalud EPS', 'Mutual Ser EPS', 'Aliansalud EPS',
            'Comfenalco Valle EPS', 'Capital Salud EPS', 'SOS (Servicio Occidental de Salud)',
        ])->map(fn($n) => ['nombre' => $n, 'activo' => true, 'created_at' => $ahora, 'updated_at' => $ahora])->all());

        DB::table('sgrh_arl')->insert(collect([
            'ARL Sura', 'Positiva Compañía de Seguros', 'Colmena Seguros ARL',
            'Seguros Bolívar ARL', 'La Equidad Seguros ARL', 'Mapfre Colombia Vida Seguros ARL',
            'Liberty Seguros ARL', 'Axa Colpatria ARL',
        ])->map(fn($n) => ['nombre' => $n, 'activo' => true, 'created_at' => $ahora, 'updated_at' => $ahora])->all());

        DB::table('sgrh_fondos_pension')->insert(collect([
            'Colpensiones (Régimen de Prima Media)', 'Porvenir', 'Protección', 'Colfondos',
            'Skandia (Old Mutual)',
        ])->map(fn($n) => ['nombre' => $n, 'activo' => true, 'created_at' => $ahora, 'updated_at' => $ahora])->all());
    }

    public function down(): void
    {
        Schema::dropIfExists('sgrh_fondos_pension');
        Schema::dropIfExists('sgrh_arl');
        Schema::dropIfExists('sgrh_eps');
    }
};
