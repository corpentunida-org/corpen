<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Área de cada perfil (rol): agrupa perfiles de una misma área en la Matriz de Permisos
 * (ej. área "asociado" -> perfiles asociado y asociadosadmon). Nullable y aditiva: el código
 * anterior no la usa. Se rellena con la regla que ya usaba la Matriz (un perfil cuyo nombre empieza
 * por el de otro más corto pertenece a esa área; los demás quedan en un área con su propio nombre).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('roles', 'area')) {
            Schema::table('roles', function (Blueprint $table) {
                $table->string('area', 60)->nullable()->after('name');
            });
        }

        $nombres = DB::table('roles')->pluck('name', 'id')->map(fn ($n) => mb_strtolower($n));
        foreach ($nombres as $id => $n) {
            $base = $nombres->filter(fn ($b) => mb_strlen($b) >= 4 && str_starts_with($n, $b))
                ->sortBy(fn ($b) => mb_strlen($b))->first() ?? $n;
            DB::table('roles')->where('id', $id)->whereNull('area')->update(['area' => $base]);
        }
    }

    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->dropColumn('area');
        });
    }
};
