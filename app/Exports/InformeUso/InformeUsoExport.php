<?php

namespace App\Exports\InformeUso;

use App\Http\Controllers\Admin\InformeUsoController;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class InformeUsoExport implements WithMultipleSheets
{
    use Exportable;

    public function __construct(private array $datos)
    {
    }

    public function sheets(): array
    {
        return [
            new SimpleArraySheet(
                $this->datos['porArea']->map(fn ($f) => [$f['area'], $f['total']]),
                ['Área', 'Total acciones'],
                'Uso por área',
            ),
            new SimpleArraySheet(
                $this->datos['porUsuario']->map(fn ($f) => [$f['nombre'], $f['total']]),
                ['Usuario', 'Total acciones'],
                'Uso por usuario',
            ),
            new SimpleArraySheet(
                $this->datos['tiempoActivo']->map(fn ($f) => [
                    $f['nombre'],
                    $f['sesiones'],
                    InformeUsoController::formatoDuracion($f['segundos_total']),
                    InformeUsoController::formatoDuracion($f['segundos_promedio']),
                ]),
                ['Usuario', 'Sesiones', 'Tiempo activo total', 'Promedio por sesión'],
                'Tiempo activo',
            ),
            new SimpleArraySheet(
                $this->datos['tiempoGestiones']->map(fn ($f) => [
                    $f['nombre'],
                    $f['gestiones'],
                    InformeUsoController::formatoDuracion($f['segundos_total']),
                    InformeUsoController::formatoDuracion($f['segundos_promedio']),
                ]),
                ['Agente', 'Gestiones', 'Tiempo total', 'Promedio por gestión'],
                'Tiempo en gestiones',
            ),
        ];
    }
}
