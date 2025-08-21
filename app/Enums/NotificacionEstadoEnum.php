<?php

namespace App\Enums;

/**
 * Define los valores permitidos para el estado de una notificación.
 */
enum NotificacionEstadoEnum: string
{
    case PENDIENTE = 'pendiente';
    case ENVIADO = 'enviado';
    case FALLIDO = 'fallido';
    case LEIDO = 'leido';
}