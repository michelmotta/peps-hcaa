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
            'file' => 'required|file|mimes:jpg,jpeg,png,gif|max:10240',
            'specialty_ids' => 'required|array',
            'specialty_ids.*' => 'exists:specialties,id',
            'workload' => 'required|integer',
            'description' => 'required|string',
        ];
    }

    /**
     * Custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'O campo nome é obrigatório.',
            'name.max' => 'O nome não pode ter mais que 255 caracteres.',

            'file.required' => 'O envio de um arquivo é obrigatório.',
            'file.file' => 'O arquivo enviado é inválido.',
            'file.mimes' => 'O arquivo deve ser uma imagem nos formatos: jpg, jpeg, png ou gif.',
            'file.max' => 'O arquivo não pode ultrapassar 10MB.',

            'specialty_ids.required' => 'É necessário selecionar ao menos uma especialidade.',
            'specialty_ids.array' => 'O campo especialidades deve ser uma lista válida.',
            'specialty_ids.*.exists' => 'Uma ou mais especialidades selecionadas não existem.',

            'workload.required' => 'A carga horária é obrigatória.',
            'workload.integer' => 'A carga horária deve ser um número inteiro.',

            'description.required' => 'A descrição é obrigatória.',
            'description.string' => 'A descrição deve ser um texto válido.',
        ];
    }
}
