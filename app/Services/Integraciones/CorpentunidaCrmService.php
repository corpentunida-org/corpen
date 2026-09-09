<?php

namespace App\Services\Integraciones;

use App\Models\Integraciones\CorpentunidaCrmConfig;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Throwable;

/**
 * Cliente para la API "Corpentunida CRM" — distinta a la API de siasoft ya
 * integrada en Exequiales vía ExequialApiService. Usa OAuth2
 * client_credentials: el token (JWT, texto plano) vence a las 4 horas, así
 * que se cachea con margen de seguridad y se renueva solo, con un reintento
 * ante un 401.
 *
 * Diseñada para "mejor esfuerzo": ningún método público lanza excepción por
 * fallos de red/autenticación/HTTP — todos devuelven bool y registran el
 * error en el log. Es intencional: el único caller de hoy (retiro/reafiliación
 * de titulares en Exequiales) no debe bloquearse si este CRM está caído o
 * las credenciales aún no están configuradas — siasoft sigue siendo el
 * sistema de registro principal de ese módulo.
 */
class CorpentunidaCrmService
{
    private const CACHE_KEY = 'corpentunida_crm.token';
    // El token real vence a las 4h; se cachea con margen para no usarlo al filo del vencimiento.
    private const CACHE_TTL_MINUTOS = 220;

    /** @var array{url:string,client_id:string,client_secret:string}|null|false */
    private $credenciales = false;

    /**
     * Prioriza la configuración guardada en BD (editable desde Admin →
     * Integraciones → CRM Corpentunida) sobre las variables de entorno — estas
     * últimas quedan solo como respaldo/arranque inicial. Memoizado por
     * instancia: el service se resuelve una vez por request.
     */
    private function credenciales(): ?array
    {
        if ($this->credenciales !== false) {
            return $this->credenciales;
        }

        $config = CorpentunidaCrmConfig::actual();
        if ($config && $config->url && $config->client_id && $config->client_secret) {
            return $this->credenciales = [
                'url' => $config->url,
                'client_id' => $config->client_id,
                'client_secret' => $config->client_secret,
            ];
        }

        $url = config('services.corpentunida_crm.url');
        $clientId = config('services.corpentunida_crm.client_id');
        $clientSecret = config('services.corpentunida_crm.client_secret');
        if ($url && $clientId && $clientSecret) {
            return $this->credenciales = [
                'url' => $url,
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
            ];
        }

        return $this->credenciales = null;
    }

    private function baseUrl(): ?string
    {
        return $this->credenciales()['url'] ?? null;
    }

    private function credencialesCompletas(): bool
    {
        return $this->credenciales() !== null;
    }

    private function obtenerToken(bool $forzarRenovacion = false): string
    {
        if ($forzarRenovacion) {
            Cache::forget(self::CACHE_KEY);
        }

        return Cache::remember(self::CACHE_KEY, now()->addMinutes(self::CACHE_TTL_MINUTOS), function () {
            $credenciales = $this->credenciales();
            if (!$credenciales) {
                throw new RuntimeException('Faltan credenciales configuradas para la API Corpentunida CRM (Admin → Integraciones → CRM Corpentunida).');
            }

            $response = Http::timeout(15)->post($credenciales['url'] . '/api/ClientAuth/token', [
                'clientId' => $credenciales['client_id'],
                'clientSecret' => $credenciales['client_secret'],
            ]);

            if (!$response->successful()) {
                throw new RuntimeException('No se pudo autenticar contra la API Corpentunida CRM: HTTP ' . $response->status());
            }

            // El endpoint documenta Content-Type text/plain: el cuerpo es el JWT
            // directo (a veces envuelto en comillas si el framework de origen
            // serializa un string plano como JSON), nunca un objeto.
            $token = trim($response->body(), " \t\n\r\0\x0B\"");

            if ($token === '') {
                throw new RuntimeException('La API Corpentunida CRM no devolvió un token válido.');
            }

            return $token;
        });
    }

    /**
     * Actualiza el estado (activo/inactivo) de un tercero en el CRM. Nunca
     * lanza: devuelve false y registra en el log ante cualquier fallo
     * (credenciales faltantes, conexión, 401 persistente, error HTTP).
     */
    public function actualizarEstadoTercero(string $codTer, bool $estado): bool
    {
        if (!$this->credencialesCompletas()) {
            Log::warning("Corpentunida CRM: credenciales no configuradas todavía, se omite sincronizar el estado de tercero {$codTer}.");
            return false;
        }

        try {
            return $this->intentarActualizarEstado($codTer, $estado);
        } catch (Throwable $e) {
            Log::warning("Corpentunida CRM: no se pudo actualizar el estado de tercero {$codTer}: " . $e->getMessage());
            return false;
        }
    }

    private function intentarActualizarEstado(string $codTer, bool $estado, bool $esReintento = false): bool
    {
        $token = $this->obtenerToken($esReintento);

        $response = Http::timeout(15)
            ->withToken($token)
            ->patch($this->baseUrl() . "/api/Terceros/{$codTer}/estado", [
                'estado' => $estado,
            ]);

        if ($response->status() === 401 && !$esReintento) {
            // Token vencido o inválido: se fuerza una renovación y se reintenta una sola vez.
            return $this->intentarActualizarEstado($codTer, $estado, true);
        }

        if (!$response->successful()) {
            Log::warning("Corpentunida CRM: PATCH Terceros/{$codTer}/estado respondió {$response->status()}: " . $response->body());
        }

        return $response->successful();
    }
}
