<?php

namespace App\Http\Requests\MeterReading;

use Illuminate\Foundation\Http\FormRequest;

class MeterReadingStoreRequest extends FormRequest
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
            'time' => 'required|array',
            'time.*' => 'required|date_format:H:i:s',
            'main_meter' => 'nullable|array',
            'main_meter.*' => 'nullable|numeric',
            'show_meter' => 'nullable|array',
            'show_meter.*' => 'nullable|numeric',
            'accuracy' => 'nullable|array',
            'accuracy.*' => 'nullable|numeric', // Allows decimal values
            'remarks' => 'nullable|array',
            'remarks.*' => 'nullable|string',
        ];
    }
}
