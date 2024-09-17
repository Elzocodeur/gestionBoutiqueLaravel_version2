<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StockArticleRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'articles' => 'required|array',
            'articles.*.id' => 'required|integer',
            'articles.*.quantity' => 'required|integer|min:1',
        ];
    }



    public function messages(): array
    {
        return [
            'articles.required' => 'La liste des articles est requise.',
            'articles.array' => 'La liste des articles doit être un tableau.',
            'articles.*.id.required' => 'L\'ID de l\'article est requis.',
            'articles.*.id.exists' => 'L\'ID de l\'article doit exister.',
            'articles.*.quantity.required' => 'La quantité est requise.',
            'articles.*.quantity.integer' => 'La quantité doit être un entier.',
            'articles.*.quantity.min' => 'La quantité doit être au moins 1.',
        ];
    }
}
