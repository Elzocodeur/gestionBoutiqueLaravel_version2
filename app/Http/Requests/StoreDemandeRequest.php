<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDemandeRequest extends FormRequest
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
            'montant' => 'required|integer|min:1',
            'articles' => 'required|array|min:1',
            'articles.*.articleId' => 'required|exists:articles,id',
            'articles.*.quantity' => 'required|integer|min:1',
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
            'montant.required' => 'Le montant est requis.',
            'montant.integer' => 'Le montant doit être un nombre entier.',
            'montant.min' => 'Le montant doit être supérieur ou égal à 1.',
            'articles.required' => 'Les articles sont requis.',
            'articles.array' => 'Les articles doivent être un tableau.',
            'articles.min' => 'Vous devez sélectionner au moins un article.',
            'articles.*.articleId.required' => 'L\'ID de l\'article est requis.',
            'articles.*.articleId.exists' => 'L\'ID de l\'article doit exister dans la liste des articles.',
            'articles.*.quantity.required' => 'La quantité est requise pour chaque article.',
            'articles.*.quantity.integer' => 'La quantité doit être un nombre entier.',
            'articles.*.quantity.min' => 'La quantité doit être supérieure ou égale à 1.',
        ];
    }
}
