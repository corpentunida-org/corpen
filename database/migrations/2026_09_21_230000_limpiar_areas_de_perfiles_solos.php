<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * El relleno inicial de roles.area convirtió cada perfil suelto en un "área" con su mismo nombre
 * (admin, cinco, certificados…), pero eso son perfiles, no áreas. Se deja sin área (NULL) a los
 * perfiles que están solos en un área con su propio nombre y se eliminan esas áreas de
 * roles_areas. Se conservan las áreas que agrupan varios perfiles (asociado, cartera) y las
 * creadas a mano que no coinciden con el nombre de un perfil.
 */
return new class extends Migration
{
    public function up(): void
    {
        // Área ADMIN = perfiles admin y admonjunior (definido por Miguel Torres).
        DB::table('roles_areas')->insertOrIgnore(['nombre' => 'admin', 'created_at' => now(), 'updated_at' => now()]);
        DB::table('roles')->whereIn('name', ['admin', 'admonjunior'])->update(['area' => 'admin']);

        $roles = DB::table('roles')->get(['id', 'name', 'area']);
        $porArea = $roles->groupBy(fn ($r) => mb_strtolower((string) $r->area));

        foreach ($roles as $r) {
            $area = mb_strtolower((string) $r->area);
            if ($area !== '' && $area === mb_strtolower($r->name) && $porArea[$area]->count() === 1) {
                DB::table('roles')->where('id', $r->id)->update(['area' => null]);
            }
        }

        // Áreas sobrantes del relleno inicial: llevan el nombre de un perfil y ya nadie las usa.
        $nombresPerfiles = DB::table('roles')->pluck('name')->map(fn ($n) => mb_strtolower($n))->all();
        $usadas = DB::table('roles')->whereNotNull('area')->pluck('area')->map(fn ($n) => mb_strtolower($n))->all();
        foreach (DB::table('roles_areas')->pluck('nombre') as $nombre) {
            if (in_array($nombre, $nombresPerfiles) && !in_array($nombre, $usadas)) {
                DB::table('roles_areas')->where('nombre', $nombre)->delete();
            }
        }
    }

    public function down(): void
    {
        // Sin reversa: volver a convertir cada perfil suelto en un área no aporta nada.
    }
};
