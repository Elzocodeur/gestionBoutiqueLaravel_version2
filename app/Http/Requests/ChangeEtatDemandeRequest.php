<?php

namespace App\Http\Requests;

use App\Models\Demande;
use App\Enums\DemandeEnum;
use Illuminate\Foundation\Http\FormRequest;

class ChangeEtatDemandeRequest extends FormRequest
{
    /**
     * Détermine si l'utilisateur est autorisé à faire cette requête.
     */
    public function authorize()
    {
        // Autorisation faite dans le contrôleur via la policy
        return true;
    }

    public function rules()
    {
        return [
            'etat' => ['string', function ($attribute, $value, $fail) {
                if (!DemandeEnum::tryFrom($value)) {
                    $fail("The selected $attribute is invalid. chose: valider, annuler");
                }
                $demande = Demande::find($this->route('id'));
                if ($demande && $demande->etat === DemandeEnum::ANNULER->value) {
                    $fail("La demande est déjà annulée et ne peut pas être modifiée.");
                }
            }],
            'motif' => ['required_if:etat,' . DemandeEnum::ANNULER->value, 'string', 'max:255'],
        ];
    }

    public function messages()
    {
        return [
            'motif.required_if' => 'Le motif est requis lorsque la demande est annulée.',
        ];
    }
}
