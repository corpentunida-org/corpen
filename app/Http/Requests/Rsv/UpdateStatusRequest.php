<?php

namespace App\Http\Requests\Rsv;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Captura el parámetro de ruta de tu resource 'statuses' => 'rsvStatus'
        $statusId = $this->route('rsvStatus')?->id ?? $this->route('rsvStatus');

        return [
            'name'      => 'required|string|max:255|unique:rsv_statuses,name,' . $statusId,
            'color_hex' => 'nullable|string|max:7',
        ];
    }
}
