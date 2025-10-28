<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Soportes\ScpSoporte;
use Illuminate\Support\Facades\Auth;

class SoporteEscaladoMail extends Mailable
{
    use Queueable, SerializesModels;

    public $soporte;
    public $escaladoPor;

    public function __construct(ScpSoporte $soporte)
    {
        $this->soporte = $soporte;
        $this->escaladoPor = Auth::user(); // Usuario que hizo el escalamiento
    }

    public function build()
    {
        return $this->subject('📢 Nuevo Soporte Escalado #' . $this->soporte->id)
                    ->view('emails.soporte_escalado');
    }
}
