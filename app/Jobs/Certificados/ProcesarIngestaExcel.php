<?php

namespace App\Jobs\Certificados;

use App\Imports\Certificados\IngestaExcelImport;
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
        private readonly string $rutaArchivo
    ) {
    }

    public function handle(): void
    {
        try {
            Excel::import(
                new IngestaExcelImport($this->numeroBloque),
                $this->rutaArchivo,
                'local'
            );

            CarSiaBloque::where('numero_bloque', $this->numeroBloque)
                ->update(['estado' => 'PROCESADO']);

            Storage::disk('local')->delete($this->rutaArchivo);
        } catch (Throwable $exception) {
            CarSiaBloque::where('numero_bloque', $this->numeroBloque)
                ->update(['estado' => 'ERROR']);

            Log::error('CERTIFICADOS Ingesta - Error procesando Excel en cola: ' . $exception->getMessage(), [
                'numero_bloque' => $this->numeroBloque,
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

        Storage::disk('local')->delete($this->rutaArchivo);
    }
}
