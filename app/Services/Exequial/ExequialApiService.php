<?php

namespace App\Services\Exequial;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Centraliza el acceso a la API externa de Exequiales (URL base, token, timeout y manejo de
 * fallos de conexión) para no repetir en cada controlador el mismo bloque
 * Http::withHeaders(...)->get/post/put/patch/delete(...) + try/catch. Antes cada controlador
 * armaba la petición a mano con env()/config() directo y sin timeout, así que una API caída
 * podía dejar el worker colgado indefinidamente y, sin try/catch, terminaba en un 500 genérico.
 *
 * No decide qué hacer ante una respuesta de error "normal" (4xx/5xx con cuerpo) — eso lo
 * sigue resolviendo cada controlador con $response->successful(); esta clase solo unifica la
 * mecánica HTTP y convierte un fallo de conexión en ExequialApiException.
 */
class ExequialApiService
{
    private function client(): PendingRequest
    {
        return Http::timeout(15)->withHeaders([
            'Authorization' => 'Bearer ' . config('services.api_produccion.token'),
        ]);
    }

    private function url(string $uri): string
    {
        return config('services.api_produccion.url') . $uri;
    }

    /** @throws ExequialApiException */
    public function get(string $uri, array $query = []): Response
    {
        return $this->send('get', $uri, $query);
    }

    /** @throws ExequialApiException */
    public function post(string $uri, array $data = []): Response
    {
        return $this->send('post', $uri, $data);
    }

    /** @throws ExequialApiException */
    public function put(string $uri, array $data = []): Response
    {
        return $this->send('put', $uri, $data);
    }

    /** @throws ExequialApiException */
    public function patch(string $uri, array $data = []): Response
    {
        return $this->send('patch', $uri, $data);
    }

    /** @throws ExequialApiException */
    public function delete(string $uri): Response
    {
        return $this->send('delete', $uri);
    }

    /** @throws ExequialApiException */
    private function send(string $method, string $uri, array $data = []): Response
    {
        try {
            return $this->client()->{$method}($this->url($uri), $data);
        } catch (ConnectionException $e) {
            Log::error("Exequiales API [{$method} {$uri}]: no se pudo conectar - " . $e->getMessage());
            throw new ExequialApiException(
                'No se pudo conectar con el servicio externo de Exequiales. Intenta de nuevo en unos minutos.',
                previous: $e,
            );
        }
    }
}
