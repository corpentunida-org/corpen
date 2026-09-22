<?php

namespace App\Http\Controllers\Exequial;

use App\Http\Controllers\Controller;
use App\Models\Exequiales\Parentescos;
use Illuminate\Support\Facades\Cache;

class ParentescosController extends Controller
{
    /**
     * El catálogo de parentescos rara vez cambia, pero showName()/show() se llaman una vez POR
     * REGISTRO dentro de bucles (dashboard, PDF, Excel de "prestar servicio") — sin esta caché,
     * cada carga de esas pantallas dispara una consulta nueva por cada registro.
     *
     * Se lee de la tabla local `parentescos` (ya usada por SGRH, Seguros y MaeTerceros) en vez
     * de la API de SiaSoft: se verificó que contiene los 33 códigos que aparecen realmente en
     * los beneficiarios de Exequiales, sin faltantes.
     */
    private function catalogo(): array
    {
        return Cache::remember('exequial.parentescos.catalogo', 300, function () {
            return Parentescos::orderBy('name')->get(['code', 'name'])->toArray();
        });
    }

    public function index()
    {
        return $this->catalogo();
    }

    public function show($nomParentesco)
    {
        foreach ($this->catalogo() as $parentesco) {
            if ($parentesco['name'] === $nomParentesco) {
                return $parentesco['code'];
            }
        }
    }

    public function showName($codParentesco)
    {
        if ($codParentesco == 'TITULAR') {
            return $codParentesco;
        }
        foreach ($this->catalogo() as $parentesco) {
            if ($parentesco['code'] === $codParentesco) {
                return $parentesco['name'];
            }
        }
    }
}
