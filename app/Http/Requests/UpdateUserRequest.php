<?php

namespace App\Http\Requests;

use App\Rules\PasswordRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nom' => 'sommetime|required|string|max:255',
            'prenom' => 'sommetime|required|string|max:255',
            'email' => 'sommetime|required|string|max:255|unique:users,email',
            'role' => 'sommetime|required|in:admin,boutiquier',
            'photo' => 'sommetime|required|image',
            'password' => ['sommetime|required', new PasswordRule()], 
            'password_confirmation' => 'required_with:password|same:password'
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
