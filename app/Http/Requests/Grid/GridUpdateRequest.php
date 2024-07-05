<?php

namespace App\Http\Requests\Grid;

use Illuminate\Foundation\Http\FormRequest;

class GridUpdateRequest extends FormRequest
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
            'fault_time' => 'sometimes|required|date_format:H:i',
            'reason' => 'sometimes|required|string|max:255',
            'photo' => 'nullable|file|mimes:jpg,png,jpeg|max:2048', // Assuming photo is an image file
            'video' => 'nullable|file|mimes:mp4,avi,mov|max:10240', // Assuming video is a video file
            'status' => 'sometimes|required|in:Solved,Unsolved',
            'solved_by' => 'sometimes|required|exists:users,id',
        ];
    }
}
