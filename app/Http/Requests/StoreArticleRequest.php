<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreArticleRequest extends FormRequest
{
    /**
     * Détermine si l'utilisateur est autorisé à faire cette demande.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Obtenir les règles de validation qui s'appliquent à la demande.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'libelle' => 'required|string|max:255',
            'price' => 'required|numeric|min:0.01',
            'quantity' => 'required|integer|min:0',
        ];
    }

    /**
     * Obtenir les messages d'erreur personnalisés pour la validation.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'libelle.required' => 'Le libellé est requis.',
            'libelle.string' => 'Le libellé doit être une chaîne de caractères.',
            'libelle.max' => 'Le libellé ne peut pas dépasser 255 caractères.',
            'price.required' => 'Le prix est requis.',
            'price.numeric' => 'Le prix doit être un nombre.',
            'price.min' => 'Le prix doit être supérieur à 0.01.',
            'quantity.required' => 'La quantité est requise.',
            'quantity.integer' => 'La quantité doit être un entier.',
            'quantity.min' => 'La quantité doit être au moins 0.',
        ];
    }
}
