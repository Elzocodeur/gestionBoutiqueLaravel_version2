<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePaiementRequest extends FormRequest
{
    /**
     * Détermine si l'utilisateur est autorisé à faire cette requête.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Retourne les règles de validation qui s'appliquent à la requête.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'montant' => 'required|numeric|min:1',
            'dette_id' => 'required|exists:dettes,id',
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
            'montant.required' => 'Le montant est requis.',
            'montant.numeric' => 'Le montant doit être un nombre.',
            'dette_id.required' => 'L\'ID de la dette est requis.',
            'client_id.required' => 'L\'ID du client est requis.',
        ];
    }
}