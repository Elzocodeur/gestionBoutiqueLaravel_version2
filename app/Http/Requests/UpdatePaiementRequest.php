<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePaiementRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'montant' => 'sometimes|required|numeric|min:0',
            'date' => 'sometimes|required|date',
            'dette_id' => 'sometimes|required|exists:dettes,id',
            'client_id' => 'sometimes|required|exists:clients,id',
        ];
    }

    /**
     * Messages personnalisés pour chaque règle de validation.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'montant.numeric' => 'Le montant doit être un nombre.',
            'date.required' => 'La date est requise.',
            'dette_id.required' => 'L\'ID de la dette est requis.',
            'client_id.required' => 'L\'ID du client est requis.',
        ];
    }
}
