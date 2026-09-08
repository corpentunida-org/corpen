<?php

namespace App\Services\Exequial;

/**
 * Se lanza cuando no fue posible conectarse a la API externa de Exequiales (timeout, DNS,
 * conexión rechazada, etc.) — nunca por respuestas de error "normales" (4xx/5xx con cuerpo),
 * esas siguen resolviéndose como Response y cada controlador decide qué hacer con ellas.
 */
class ExequialApiException extends \RuntimeException
{
}
