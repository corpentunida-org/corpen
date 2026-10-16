<?php

namespace App\Http\Requests\Rsv;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use App\Rules\Rsv\InmuebleDisponible;

class UpdateReservaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        $reservaId = $this->route('reserva') ?? $this->id;
        $estadosCancelados = [4, 5];

        return [
            'id_rsv_catalogo_inmueble' => [
                'sometimes',
                'required',
                'exists:rsv_catalogo_inmueble,id',
                new InmuebleDisponible(
                    inmuebleId: $this->id_rsv_catalogo_inmueble ?? $this->reserva?->id_rsv_catalogo_inmueble,
                    fechaInicio: $this->fecha_inicio,
                    fechaFin: $this->fecha_fin,
                    reservaIdIgnore: $reservaId,
                    estadosInactivos: $estadosCancelados
                )
            ],
            'id_user'                => 'sometimes|required|integer',
            'id_rsv_statuses'        => 'sometimes|required|exists:rsv_statuses,id',
            'fecha_inicio'           => 'sometimes|required|date',
            'fecha_fin'              => 'sometimes|required|date|after:fecha_inicio',
            'monto_total'            => 'sometimes|required|numeric|min:0',
            'id_rsv_origen_reservas' => 'sometimes|required|exists:rsv_origen_reservas,id',
            'comentario_reserva'     => 'nullable|string',
        ];
    }
}
