<?php

namespace App\Mail;

use App\Models\Soportes\ScpSoporte;
use App\Models\Soportes\ScpObservacion;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SoporteEscaladoMail extends Mailable
{
    use Queueable, SerializesModels;

    public $soporte;
    public $observacion;
    public $destinatarioTipo; // 'escalado' o 'creador'

    /**
     * Create a new message instance.
     *
     * @param ScpSoporte $soporte
     * @param ScpObservacion $observacion
     * @param string $destinatarioTipo
     * @return void
     */
    public function __construct(ScpSoporte $soporte, ScpObservacion $observacion, $destinatarioTipo = 'escalado')
    {
        $this->soporte = $soporte;
        $this->observacion = $observacion;
        $this->destinatarioTipo = $destinatarioTipo;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if ($this->destinatarioTipo === 'escalado') {
            $subject = "Nuevo soporte escalado: #{$this->soporte->id}";
            $view = 'emails.soportes.escalado_usuario';
        } else {
            $subject = "Su soporte ha sido escalado: #{$this->soporte->id}";
            $view = 'emails.soportes.escalado_creador';
        }

        return $this->subject($subject)
                    ->view($view)
                    ->with([
                        'soporte' => $this->soporte,
                        'observacion' => $this->observacion,
                        'destinatarioTipo' => $this->destinatarioTipo,
                    ]);
    }
}