<?php

namespace App\Http\Controllers\Interacciones\Concerns;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Compartido por los catálogos de Interacciones que se administran por área (Tipos, Resultados,
 * ...): resolver el área del perfil de quien administra, si puede elegir cualquiera, y bloquear
 * tocar un registro de OTRA área. Mismo criterio en los tres: roles.area (Matriz de Permisos),
 * prefiriendo un rol que SÍ tenga área sobre uno que no (perfiles legado con más de un rol).
 */
trait GestionaAreaDeCatalogo
{
    private function areaDelUsuario(): ?string
    {
        return DB::table('actions')
            ->join('roles', 'roles.id', '=', 'actions.role_id')
            ->where('actions.user_id', Auth::id())
            ->orderByRaw('roles.area is null')
            ->value('roles.area');
    }

    /** Con listado.todos se administra el catálogo de cualquier área; sin él, solo el propio. */
    private function puedeElegirArea(): bool
    {
        return auth()->user()->hasDirectPermission('interacciones.listado.todos');
    }
}
