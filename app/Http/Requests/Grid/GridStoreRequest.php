<?php

namespace App\Http\Requests\Grid;

use Illuminate\Foundation\Http\FormRequest;

class GridStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'gone_time' => 'required|date_format:H:i',
            'charge_time' => 'nullable|date_format:H:i',
            'unit_one_sync_time' => 'nullable|date_format:H:i',
            'unit_two_sync_time' => 'nullable|date_format:H:i',
        ];
    }
}
