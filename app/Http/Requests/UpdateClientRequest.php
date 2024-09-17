<?php

namespace App\Http\Requests;

use App\Rules\PasswordRule;
use App\Rules\TelephoneRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateClientRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'surname' => ['sometimes','required', 'string', 'max:255', 'unique:clients,surname'],
            'adresse' => ['sometimes','nullable', 'string', 'max:255'],
            'telephone' => ['sometimes','required', 'unique:clients,telephone', new TelephoneRule()],
            'categorie' => ['sometimes','sometimes', 'required', 'string', 'in:gold,bronze,silver'],
            'max_montant' => ['sometimes','required_if:categorie,silver', 'integer'],
            'user' => ['sometimes', 'array'],
            'user.nom' => ['sometimes','required_with:user', 'string'],
            'user.prenom' => ['sometimes','required_with:user', 'string'],
            'user.photo' => ['sometimes','required_with:user', 'image'],
            'user.email' => ['sometimes','required_with:user', 'string', 'email', 'max:255', 'unique:users,email'],
            'user.password' => ['sometimes','required_with:user', 'string', 'min:8', 'confirmed', new PasswordRule()],
        ];
    }

    function messages()
    {

        return [
            'surname.required' => "Le surnom est obligatoire.",
            'surname.string' => "Le surnom doit être une chaîne de caractères.",
            'surname.max' => "Le surnom ne doit pas dépasser 255 caractères.",
            'surname.unique' => "Ce surnom est déjà utilisé.",

            'adresse.string' => "L'adresse doit être une chaîne de caractères.",
            'adresse.max' => "L'adresse ne doit pas dépasser 255 caractères.",

            'telephone.required' => "Le téléphone est obligatoire.",
            'telephone.unique' => "Ce téléphone est déjà utilisé.",

            'categorie.required' => "La catégorie est obligatoire.",
            'categorie.string' => "La catégorie doit être une chaîne de caractères.",
            'categorie.in' => "La catégorie doit être l'une des valeurs suivantes : gold, bronze, silver.",

            'max_montant.required_if' => "Le montant maximal est obligatoire pour la catégorie silver.", 
            'max_montant.integer' => "Le montant maximal doit être un nombre entier.", 

            'user.nom.required_with' => 'Le nom est obligatoire lorsque l\'utilisateur est fourni.',
            'user.nom.string' => 'Le nom doit être une chaîne de caractères.',
            'user.prenom.required_with' => 'Le prénom est obligatoire lorsque l\'utilisateur est fourni.',
            'user.prenom.string' => 'Le prénom doit être une chaîne de caractères.',
            'user.photo.required_with' => 'La photo est obligatoire lorsque l\'utilisateur est fourni.',
            'user.photo.image' => 'La photo doit être une image.',
            'user.email.required_with' => 'L\'email est obligatoire lorsque l\'utilisateur est fourni.',
            'user.email.string' => 'L\'email doit être une chaîne de caractères.',
            'user.email.email' => 'L\'email doit être une adresse email valide.',
            'user.email.max' => 'L\'email ne doit pas dépasser 255 caractères.',
            'user.email.unique' => 'Cet email est déjà utilisé.',
            'user.password.required_with' => 'Le mot de passe est obligatoire lorsque l\'utilisateur est fourni.',
            'user.password.string' => 'Le mot de passe doit être une chaîne de caractères.',
            'user.password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
            'user.password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
        ];
    }
}
