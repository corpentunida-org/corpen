<?php

namespace App\Services\Integraciones;

use Illuminate\Support\Facades\Http;
use Exception;

class PastorService
{
    /**
     * Consume el endpoint de Pastors y retorna los datos en un solo objeto.
     *
     * @param string|int $nid Número de documento a consultar
     * @return object
     * @throws Exception
     */
    public function obtenerPastor($nid): object
    {
        $url = env('API_PRODUCCION') . "/api/Pastors";
        $token = env('TOKEN_ADMIN');

        // 1. Realizamos la solicitud GET
        $response = Http::withToken($token)->get($url, [
            'DocumentId' => $nid
        ]);

        // 2. Verificamos errores
        if ($response->failed()) {
            throw new Exception('Error al consumir la API Pastors: ' . $response->body());
        }

        // 3. Obtenemos el array original
        $data = $response->json();

        // 4. Mapeamos y devolvemos todo como un objeto directamente (stdClass)
        return (object) [
            'documentId'   => $data['documentId'] ?? '',
            'name'         => $data['name'] ?? '',
            'email'        => $data['email'] ?? '',
            'type'         => (int) ($data['type'] ?? 0),
            'district'     => $data['district'] ?? 'NINGUNO',
            'birthdate'    => $data['birthdate'] ?? '',
            'phone'        => $data['phone'] ?? '',
            'congregation' => $data['congregation'] ?? '',
        ];
    }
}
