<?php

namespace App\Services\Exequial;

/**
 * Se lanza cuando no fue posible conectarse a la API externa de Exequiales (timeout, DNS,
 * conexión rechazada, etc.), o cuando Siasoft rechaza el token (401/403 — TOKEN_ADMIN vencido o
 * inválido, ver ExequialApiService::send()). El resto de respuestas de error "normales" (4xx/5xx
 * con cuerpo, ej. un 404 genuino) siguen resolviéndose como Response y cada controlador decide
 * qué hacer con ellas.
 */
class ExequialApiException extends \RuntimeException
{
}
