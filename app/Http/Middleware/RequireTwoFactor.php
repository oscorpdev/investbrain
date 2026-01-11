<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireTwoFactor
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (! $user) {
            return $next($request);
        }

        $isConfirmed = ! empty($user->two_factor_confirmed_at);

        // Sta alleen setup/challenge/logout + Fortify endpoints toe totdat 2FA bevestigd is.
        if (! $isConfirmed) {
            if (
                $request->routeIs('two-factor.setup') ||
                $request->is('user/confirm-password') ||
                $request->is('user/two-factor-authentication') ||
                $request->is('user/confirmed-two-factor-authentication') ||
                $request->is('user/two-factor-qr-code') ||
                $request->is('user/two-factor-recovery-codes') ||
                $request->is('two-factor-challenge') ||
                $request->is('logout')
            ) {
                return $next($request);
            }

            return redirect()->route('two-factor.setup');
        }

        return $next($request);
    }
}
