<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginUserRequest extends FormRequest
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
            'username' => 'required|email|exists:users,username',
            'password' => 'required|string|min:6',
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
            'username.required' => 'O nome de usuário é obrigatório.',
            'username.exists' => 'Não existe um usuário cadastrado para o nome de usuário informado.',
            'password.required' => 'A senha é obrigatória.',
            'password.min' => 'A senha deve ser no mínimo 6 caracteres.',
        ];
    }
}
