<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePerfilRequest extends FormRequest
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
            'email' => 'required|email|max:255|unique:users,email',
            'cpf' => 'required|max:255|unique:users',
            'username' => 'required|max:255|unique:users',
            'password' => 'required|min:6|max:255|confirmed',
            'biography' => 'nullable|string',
            'file' => 'nullable|file|mimes:jpg,jpeg,png,gif|max:2048',
            'terms' => 'accepted',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'terms.accepted' => 'Você deve aceitar os Termos de Uso para criar uma conta.',
        ];
    }
}
