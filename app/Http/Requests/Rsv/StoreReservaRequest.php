<?php

namespace App\Http\Requests\Rsv;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use App\Rules\Rsv\InmuebleDisponible;

class StoreReservaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        // IDs de estados cancelados/rechazados que liberen agenda (Ajusta segun tu tabla rsv_statuses)
        $estadosCancelados = [4, 5];

        return [
            'id_rsv_catalogo_inmueble' => [
                'required',
                'exists:rsv_catalogo_inmueble,id',
                new InmuebleDisponible(
                    inmuebleId: $this->id_rsv_catalogo_inmueble,
                    fechaInicio: $this->fecha_inicio,
                    fechaFin: $this->fecha_fin,
                    reservaIdIgnore: null,
                    estadosInactivos: $estadosCancelados
                )
            ],
            'id_user'                => 'required|integer',
            'id_rsv_statuses'        => 'required|exists:rsv_statuses,id',
            'fecha_inicio'           => 'required|date',
            'fecha_fin'              => 'required|date|after:fecha_inicio',
            'monto_total'            => 'required|numeric|min:0',
            'id_rsv_origen_reservas' => 'required|exists:rsv_origen_reservas,id',
            'comentario_reserva'     => 'nullable|string',
        ];
    }
}
