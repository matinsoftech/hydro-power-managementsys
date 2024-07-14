<?php

namespace App\Http\Requests\Station;

use Illuminate\Foundation\Http\FormRequest;

class StationStoreRequest extends FormRequest
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
            'station_level' => 'required|integer',
            'parent_id' => 'nullable|exists:stations,id',
            'name' => 'required|string|max:255',
            'map_name' => 'nullable|string|max:255',
            'capacity' => 'required|integer',
            'line_man_name' => 'required|string|max:255',
            'manager' => 'required|string|max:255',
            'start_date' => 'required|date',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ];
    }
}
