<?php

namespace App\Services\Auth;

use App\Facades\UserFacade;
use App\Services\Auth\Interfaces\AuthentificationServiceInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthentificationPassportService implements AuthentificationServiceInterface
{
    public function authentificate(array $credentials)
    {
        $user = UserFacade::findByColumn("email", $credentials['email'], many: false);

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken('auth_token')->accessToken;

        return [
            'token' => $token,
            'user' => $user
        ];
    }

    public function logout($user)
    {
        $user->tokens()->delete();
        return ['message' => 'Successfully logged out'];
    }
}
