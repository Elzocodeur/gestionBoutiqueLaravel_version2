<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDetteRequest extends FormRequest
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
            'montant' => 'sometimes|integer|min:0',
            'date' => 'sometimes|date',
            'client_id' => 'sometimes|exists:clients,id',
            'articles' => 'sometimes|array|min:1',
            'articles.*.article_id' => 'required_with:articles|exists:articles,id',
            'articles.*.quantity' => 'required_with:articles|integer|min:1',
            'articles.*.price' => 'required_with:articles|numeric|min:0',
            'echeance' => 'sometimes|required|date|after:today', 
        ];
    }
}
