<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDetteRequest extends FormRequest
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
    public function rules()
    {
        return [
            'montant' => 'required|integer|min:1',
            'clientId' => 'required|exists:clients,id',
            'articles' => 'required|array|min:1',
            'articles.*.articleId' => 'required|exists:articles,id',
            'articles.*.quantity' => 'required|integer|min:1',
            'articles.*.price' => 'required|numeric|min:1',
            'paiement' => 'sometimes|required|array',
            'paiement.montant' => 'required_with:paiement|numeric|min:1',
            'echeance' => 'required|date|after:today', 
        ];
    }

    public function messages()
    {
        return [
            'montant.required' => 'Le montant est obligatoire.',
            'montant.integer' => 'Le montant doit être un nombre entier.',
            'montant.min' => 'Le montant doit être au moins de 1.',

            'clientId.required' => 'Le client est obligatoire.',
            'clientId.exists' => 'Le client sélectionné n\'existe pas.',

            'articles.required' => 'Les articles sont obligatoires.',
            'articles.array' => 'Les articles doivent être un tableau.',
            'articles.min' => 'Vous devez ajouter au moins un article.',
            'articles.*.articleId.required' => 'L\'ID de l\'article est obligatoire.',
            'articles.*.articleId.exists' => 'L\'article sélectionné n\'existe pas.',
            'articles.*.quantity.required' => 'La quantité est obligatoire.',
            'articles.*.quantity.integer' => 'La quantité doit être un nombre entier.',
            'articles.*.quantity.min' => 'La quantité doit être au moins de 1.',
            'articles.*.price.required' => 'Le prix est obligatoire.',
            'articles.*.price.numeric' => 'Le prix doit être un nombre.',
            'articles.*.price.min' => 'Le prix doit être au moins de 1.',

            'paiement.montant.required_with' => 'Le montant du paiement est obligatoire lorsque le paiement est présent.',
            'paiement.montant.numeric' => 'Le montant du paiement doit être un nombre.',
            'paiement.montant.min' => 'Le montant du paiement doit être au moins de 1.',

            'echeance.required' => 'L\'échéance est obligatoire.',
            'echeance.date' => 'L\'échéance doit être une date valide.',
            'echeance.after' => 'L\'échéance doit être une date postérieure à aujourd\'hui.',
        ];
    }
}