<?php

namespace App\Http\Controllers\Exequial;

use App\Http\Controllers\Controller;
use App\Services\Exequial\ExequialApiException;
use App\Services\Exequial\ExequialApiService;
use Illuminate\Support\Facades\Cache;

class ParentescosController extends Controller
{
    public function __construct(private ExequialApiService $api)
    {
    }

    /**
     * El catálogo de parentescos rara vez cambia, pero showName()/show() se llaman una vez POR
     * REGISTRO dentro de bucles (dashboard, PDF, Excel de "prestar servicio") — sin esta caché,
     * cada carga de esas pantallas dispara una petición HTTP nueva a la API externa por cada
     * registro, lo que puede volverlas muy lentas o provocar timeouts con muchos registros.
     */
    private function catalogo(): array
    {
        return Cache::remember('exequial.parentescos.catalogo', 300, function () {
            try {
                $response = $this->api->get('/api/Exequiales/Relationship');
                return $response->successful() ? $response->json() : [];
            } catch (ExequialApiException $e) {
                return [];
            }
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
