<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MaintenanceMode
{
    public function handle(Request $request, Closure $next): Response
    {
        if (app()->isDownForMaintenance()) {
            if (!auth()->check() || !auth()->user()->hasRole('Super Admin')) {
                abort(503);
            }
        }

        return $next($request);
    }
}
