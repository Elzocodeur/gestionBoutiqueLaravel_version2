<?php

namespace App\Http\Requests;

use App\Rules\PasswordRule;
use App\Models\Client;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class RegisterRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'photo' => 'required|image',
            'client_id' => 'required|integer|exists:clients,id',
            'email' => 'required|string|max:255|unique:users,email', 
            'password' => ['required', 'confirmed', new PasswordRule()],
        ];
    }


    protected function withValidator(Validator $validator)
    {
        $validator->after(function ($validator) {
            $client_id = $this->input('client_id');
            $existingClient = Client::where('id', $client_id)
                ->whereNull('user_id')
                ->first();

            if (!$existingClient) {
                $validator->errors()->add('client_id', 'Ce client n\'existe pas ou est déjà associé à un utilisateur.');
            }
        });
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