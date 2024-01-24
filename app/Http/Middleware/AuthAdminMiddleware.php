<?php

namespace App\Http\Middleware;

use App\Models\User;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthAdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->header("Authorization");

        if (!$token) {
            return response()->json([
                "error" => "Unauthorized"
            ])->setStatusCode(401);
        }

        $user = User::where('token', $token)->first();
        if (!$user) {
            return response()->json([
                "error" => "Unauthorized"
            ])->setStatusCode(401);
        }

        if ($this->isTokenExpired($user->token_expiry)) {
            return response()->json([
                "error" => "Unauthorized"
            ])->setStatusCode(401);
        }

        if ($user->is_admin == false) {
            return response()->json([
                "error" => "Unauthorized"
            ])->setStatusCode(401);
        }

        Auth::login($user);
        return $next($request);
    }

    protected function isTokenExpired($expiry): bool
    {
        $expiryTime = Carbon::parse($expiry);
        return $expiryTime->isPast();
    }
}
