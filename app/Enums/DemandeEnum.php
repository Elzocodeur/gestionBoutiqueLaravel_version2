<?php

namespace App\Enums;

enum DemandeEnum: string
{
    case EN_COURS = 'en_cours';
    case ANNULER = 'annuler';
    case VALIDER = 'valider';
}

