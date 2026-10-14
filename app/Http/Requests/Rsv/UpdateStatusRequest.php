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
        // El nombre del parámetro viene de tu web.php: parameters(['statuses' => 'rsvStatus'])
        $statusId = $this->route('rsvStatus');

        return [
            'name'      => 'required|string|max:255|unique:rsv_statuses,name,' . $statusId,
            'color_hex' => 'nullable|string|max:7',
        ];
    }
}
