<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * SegPolizaController::store() (crear póliza) y el modelo SegTercero ya intentan guardar
 * 'telefono' y 'distrito' (están en $fillable, y create.blade.php los pide como campos del
 * formulario) pero la tabla SEG_terceros nunca tuvo esas columnas — cualquier intento de crear
 * un tercero nuevo desde "Crear Póliza" se caía con "Column not found: telefono". Varchar para
 * ambas, igual que el resto del sistema (MaeTerceros.tel/cel/cod_dist también son varchar).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('SEG_terceros', function (Blueprint $table) {
            if (! Schema::hasColumn('SEG_terceros', 'telefono')) {
                $table->string('telefono', 20)->nullable()->after('fechaNacimiento');
            }
            if (! Schema::hasColumn('SEG_terceros', 'distrito')) {
                $table->string('distrito', 100)->nullable()->after('genero');
            }
        });
    }

    public function down(): void
    {
        Schema::table('SEG_terceros', function (Blueprint $table) {
            if (Schema::hasColumn('SEG_terceros', 'telefono')) {
                $table->dropColumn('telefono');
            }
            if (Schema::hasColumn('SEG_terceros', 'distrito')) {
                $table->dropColumn('distrito');
            }
        });
    }
};
