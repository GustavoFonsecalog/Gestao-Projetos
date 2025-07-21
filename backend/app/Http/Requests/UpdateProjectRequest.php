<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProjectRequest extends FormRequest
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
            'title' => 'sometimes|required|string|max:255',
            'client_name' => 'sometimes|required|string|max:255',
            'phase' => 'sometimes|required|string|max:255',
            'active' => 'sometimes|boolean',
            'users' => 'array',
            'users.*' => 'exists:users,id',
        ];
    }
}
