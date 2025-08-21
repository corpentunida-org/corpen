<?php

namespace App\Enums;

enum AcuerdoEstadoEnum: string
{
    case VIGENTE = 'vigente';
    case INCUMPLIDO = 'incumplido';
    case PAGADO = 'pagado';
    case CANCELADO = 'cancelado';

    // Nota: Asegúrate de que estos valores ('vigente', 'incumplido', etc.)
    // coincidan exactamente con los que definiste en tu migración para la columna ENUM.
}