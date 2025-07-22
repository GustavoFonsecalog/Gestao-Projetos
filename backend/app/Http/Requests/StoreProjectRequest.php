<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProjectRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'client_name' => 'required|string|max:255',
            'phase' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'data_inicio' => 'nullable|date',
            'data_fim' => 'nullable|date',
            'status' => 'required|string',
            'prioridade' => 'required|string',
            'orcamento_estimado' => 'nullable|numeric',
            'orcamento_real' => 'nullable|numeric',
            'users' => 'array',
            'users.*' => 'exists:users,id',
            'signature_base64' => 'nullable|string',
        ];
    }
}
