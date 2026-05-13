<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogLastActivity
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $user = auth()->user();
            $user->last_login_at = now();
            $user->last_login_ip = $request->ip();
            $user->save();
        }

        return $next($request);
    }
}
