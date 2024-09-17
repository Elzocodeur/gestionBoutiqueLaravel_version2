<?php

namespace App\Http\Middleware;

use App\Facades\RoleFacade;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;



class CheckRoleMiddleware
{

    public function handle(Request $request, Closure $next, $roles)
    {


        $roles = explode(',', $roles);

        $roleLibelle = RoleFacade::getLibelle(Auth::user()->role_id);

        if (Auth::check() && in_array($roleLibelle, $roles)) {
            return $next($request);
        }

        return [
            "message" => "Unauthorized",
            "status" => 403
        ];
    }
}
