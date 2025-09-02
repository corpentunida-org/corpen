<?php

namespace App\Services;

use Google\Client; // <<< --- ¡Esta línea es clave!
use Google\Service\Drive;
use Google\Service\Drive\DriveFile;
use Exception;
use Illuminate\Support\Facades\Log;

class GoogleDriveService
{
    protected $client;
    protected $driveService;
    protected $folderId;

    public function __construct()
    {
        $this->client = new Client(); // Debería funcionar después de la importación
 
        // Carga las credenciales de Google
        // Asegúrate de que la ruta sea correcta y el archivo JSON accesible
        $credentialsPath = storage_path(env('GOOGLE_APPLICATION_CREDENTIALS', 'app/google/dbbrayam-11d49-e0e96309c13a.json'));
        
        if (!file_exists($credentialsPath)) {
            Log::error('Google Drive Service: Archivo de credenciales no encontrado en ' . $credentialsPath);
            throw new Exception('Google Drive Service: Archivo de credenciales no encontrado.');
        }

        $this->client->setAuthConfig($credentialsPath);
        $this->client->setScopes([
            Drive::DRIVE, // Acceso completo a Google Drive (considera DRIVE_FILE si solo trabajas con archivos creados por la app)
            Drive::DRIVE_FILE // Acceso a archivos creados o abiertos por la aplicación
        ]);

        // Crea el servicio de Google Drive
        $this->driveService = new Drive($this->client);

        // Opcional: Establece el ID de la carpeta por defecto si siempre subes a la misma.
        // Puedes obtener el ID de la URL de la carpeta en Google Drive.
        // Por ejemplo, si tu URL es https://drive.google.com/drive/folders/abcdefg12345, el ID es abcdefg12345
        $this->folderId = env('GOOGLE_DRIVE_FOLDER_ID', null); // Definir en .env
    }

    /**
     * Sube un archivo a Google Drive y devuelve su URL de visualización.
     *
     * @param string $filePath Ruta temporal del archivo en el servidor.
     * @param string $fileName Nombre deseado del archivo en Google Drive.
     * @param string $mimeType Tipo MIME del archivo (ej. 'image/jpeg', 'application/pdf').
     * @return string|null La URL pública del archivo o null en caso de error.
     */
    public function uploadFile(string $filePath, string $fileName, string $mimeType): ?string
    {
        try {
            $fileMetadata = new DriveFile([
                'name'     => $fileName,
                'parents'  => $this->folderId ? [$this->folderId] : [], // Si hay folderId, se asigna a la carpeta
                'mimeType' => $mimeType,
            ]);

            $content = file_get_contents($filePath);

            $file = $this->driveService->files->create($fileMetadata, [
                'data'             => $content,
                'mimeType'         => $mimeType,
                'uploadType'       => 'multipart',
                'fields'           => 'id, webViewLink',
                'supportsAllDrives'=> true,              // 👈 CLAVE
            ]);


            // Hacer el archivo público (opcional, pero necesario si quieres una URL pública)
            $this->makeFilePublic($file->id);

            Log::info('Archivo subido a Google Drive: ' . $file->webViewLink);
            return $file->webViewLink; // Devuelve la URL de visualización

        } catch (Exception $e) {
            Log::error('Error al subir archivo a Google Drive: ' . $e->getMessage(), [
                'file_name' => $fileName,
                'mime_type' => $mimeType,
                'error_code' => $e->getCode()
            ]);
            return null;
        }
    }

    /**
     * Hace un archivo de Google Drive accesible públicamente.
     *
     * @param string $fileId El ID del archivo de Google Drive.
     * @return void
     */
    protected function makeFilePublic(string $fileId): void
    {
        try {
            $permission = new \Google\Service\Drive\Permission([
                'type' => 'anyone',
                'role' => 'reader',
            ]);

            $this->driveService->permissions->create($fileId, $permission, ['fields' => 'id']);
            Log::info('Archivo de Google Drive hecho público: ' . $fileId);

        } catch (Exception $e) {
            Log::error('Error al hacer público el archivo de Google Drive ' . $fileId . ': ' . $e->getMessage());
            // Dependiendo de tu lógica, podrías relanzar la excepción o manejarla de otra forma.
        }
    }

    /**
     * Elimina un archivo de Google Drive.
     *
     * @param string $fileId El ID del archivo de Google Drive.
     * @return bool True si se eliminó exitosamente, false en caso contrario.
     */
    public function deleteFile(string $fileId): bool
    {
        try {
            $this->driveService->files->delete($fileId);
            Log::info('Archivo eliminado de Google Drive: ' . $fileId);
            return true;
        } catch (Exception $e) {
            Log::error('Error al eliminar archivo de Google Drive ' . $fileId . ': ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Extrae el ID del archivo de Google Drive de una URL de visualización o de descarga.
     * Esto es útil para obtener el ID real si solo guardaste la URL.
     *
     * @param string $url La URL del archivo de Google Drive.
     * @return string|null El ID del archivo o null si no se puede extraer.
     */
    public function getFileIdFromUrl(string $url): ?string
    {
        // Patrones para URLs de Google Drive
        $patterns = [
            '#id=([a-zA-Z0-9_-]+)#', // drive.google.com/uc?id=...
            '#file/d/([a-zA-Z0-9_-]+)/view#', // drive.google.com/file/d/.../view
            '#file/d/([a-zA-Z0-9_-]+)#', // drive.google.com/file/d/... (versión corta)
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $url, $matches)) {
                return $matches[1];
            }
        }
        return null;
    }
}