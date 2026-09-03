<?php

namespace App\Jobs\Certificados;

use App\Imports\Certificados\IngestaExcelImport;
use App\Models\Certificados\CarSiaApi;
use App\Models\Certificados\CarSiaBloque;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Throwable;

class ProcesarIngestaExcel implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 1800;
    public int $tries = 3;

    public function __construct(
        private readonly int $numeroBloque,
        private readonly string $rutaArchivo,
        private readonly string $discoArchivo
    ) {
    }

    public function handle(): void
    {
        $this->writeRuntimeLog('Iniciando importación', [
            'numero_bloque' => $this->numeroBloque,
            'disco' => $this->discoArchivo,
            'archivo' => $this->rutaArchivo,
        ]);

        try {
            if (!Storage::disk($this->discoArchivo)->exists($this->rutaArchivo)) {
                throw new \RuntimeException("El archivo de ingesta no existe en el disco {$this->discoArchivo}: {$this->rutaArchivo}");
            }

            Excel::import(
                new IngestaExcelImport($this->numeroBloque),
                $this->rutaArchivo,
                $this->discoArchivo
            );

            $registros = CarSiaApi::where('numero_bloque', $this->numeroBloque)->count();
            CarSiaBloque::where('numero_bloque', $this->numeroBloque)
                ->update(['estado' => 'PROCESADO']);

            Storage::disk($this->discoArchivo)->delete($this->rutaArchivo);
            $this->writeRuntimeLog('Importación finalizada', [
                'numero_bloque' => $this->numeroBloque,
                'registros' => $registros,
            ]);
        } catch (Throwable $exception) {
            CarSiaBloque::where('numero_bloque', $this->numeroBloque)
                ->update(['estado' => 'ERROR']);

            Log::error('CERTIFICADOS Ingesta - Error procesando Excel en cola: ' . $exception->getMessage(), [
                'numero_bloque' => $this->numeroBloque,
            ]);
            $this->writeRuntimeLog('Importación fallida', [
                'numero_bloque' => $this->numeroBloque,
                'error' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }

    public function failed(?Throwable $exception): void
    {
        if ($exception) {
            Log::error('CERTIFICADOS Ingesta - Job agotado: ' . $exception->getMessage(), [
                'numero_bloque' => $this->numeroBloque,
            ]);
        }

        Storage::disk($this->discoArchivo)->delete($this->rutaArchivo);
    }

    private function writeRuntimeLog(string $message, array $context = []): void
    {
        fwrite(STDERR, $message . ' ' . json_encode($context, JSON_UNESCAPED_SLASHES) . PHP_EOL);
    }
}
