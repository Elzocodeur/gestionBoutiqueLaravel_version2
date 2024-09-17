<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class TelephoneRule implements ValidationRule
{

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!preg_match('/^(77|78|70|76|75|33)[0-9]{7}$/', $value)) {
            $fail('Le numéro de téléphone :attribute n\'est pas valide. Il doit commencer par 77, 78, 70, 76, 75 ou 33 et contenir 9 chiffres au total.');
        }
    }
}