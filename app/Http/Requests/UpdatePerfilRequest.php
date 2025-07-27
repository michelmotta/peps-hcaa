<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePerfilRequest extends FormRequest
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
            'name' => 'required|max:255',
            'email' => [
                'required',
                'max:255',
                Rule::unique('users', 'email')->ignore($this->route('user')),
            ],
            'cpf' => [
                'required',
                'max:255',
                Rule::unique('users', 'cpf')->ignore($this->route('user')),
            ],
            'username' => [
                'required',
                'max:255',
                Rule::unique('users', 'username')->ignore($this->route('user')),
            ],
            'password' => 'nullable|min:6|max:255|confirmed',
            'file' => 'nullable|file|mimes:jpg,jpeg,png,gif|max:2048',
            'biography' => 'nullable|string',
        ];
    }
}
