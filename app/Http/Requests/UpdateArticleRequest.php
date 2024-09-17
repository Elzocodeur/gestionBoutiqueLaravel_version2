<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateArticleRequest extends FormRequest
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
            'libelle' => 'sometimes|string|max:255|unique:articles,libelle',
            'price' => 'sometimes|numeric|min:0.01',
            'quantity' => 'sometimes|integer|min:0',
        ];
    }

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