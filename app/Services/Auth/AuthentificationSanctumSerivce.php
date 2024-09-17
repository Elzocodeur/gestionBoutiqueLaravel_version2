<?php

namespace App\Services\Auth;

use App\Facades\UserFacade;
use App\Services\Auth\Interfaces\AuthentificationServiceInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AuthentificationSanctumSerivce implements AuthentificationServiceInterface
{

    public function authentificate(array $credentials)
    {
        if (!Auth::attempt($credentials)) {
            throw ValidationException::withMessages([
                'email' => ['Les informations d\'identification fournies sont incorrectes.'],
            ]);
        }
        $user = UserFacade::emailExist($credentials['email']);
        $token = $user->createToken('auth_token')->plainTextToken;

        Log::info('Sanctum Token:', ['token' => $token]); // Ajoute cette ligne pour vérifier le token

        return [
            'token' => $token,
            'user' => $user
        ];
    }

    public function logout($user)
    {
        $user->tokens()->delete();
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return ['message' => 'Successfully logged out'];
    }
}
