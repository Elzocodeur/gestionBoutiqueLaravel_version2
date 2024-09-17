<?php

namespace App\Http\Requests;

use App\Rules\PasswordRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Autoriser cette requête si besoin est.
    }

    public function rules(): array
    {
        return [
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|string|max:255|unique:users,email',
            'role' => 'required|in:admin,boutiquier',
            'photo' => 'required|image',
            'password' => ['required', new PasswordRule()], // Appliquer PasswordRule ici
            'password_confirmation' => 'required_with:password|same:password' // Utiliser la règle 'confirmed' proprement
        ];
    }

    public function messages(): array
    {
        return [
            'photo.required' => 'Le photo est obligatoire',
            'photo.image' => 'Le photo doit être un image',
            'nom.required' => 'Le nom est obligatoire.',
            'prenom.required' => 'Le prénom est obligatoire.',
            'email.required' => 'L\'email est obligatoire.',
            'email.unique' => 'Cet email est déjà utilisé.',
            'role.required' => 'Le rôle est obligatoire.',
            'role.in' => 'Le rôle doit être admin ou boutiquier.',
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
        ];
    }
}
