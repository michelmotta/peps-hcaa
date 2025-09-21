<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
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
            'biography' => 'nullable',
            'expertise' => 'nullable',
            'file' => 'nullable|file|mimes:jpg,jpeg,png,gif|max:2048',
            'sector_id' => 'required|exists:sectors,id',
            'profiles' => 'nullable|array',
            'profiles.*' => 'exists:profiles,id',
        ];
    }
}
