<?php

namespace App\Services\Auth\Interfaces;

interface AuthentificationServiceInterface
{
    public function authentificate(array $credentials);

    public function logout($user);    
}
