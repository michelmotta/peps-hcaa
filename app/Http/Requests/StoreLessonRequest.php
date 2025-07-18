<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLessonRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'file' => 'required|file|mimes:jpg,jpeg,png,gif|max:2048',
            'specialty_ids' => 'required|array',
            'specialty_ids.*' => 'exists:specialties,id',
            'workload' => 'required|integer',
            'description' => 'required|string',
        ];
    }
}
