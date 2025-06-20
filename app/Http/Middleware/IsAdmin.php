<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::guard('admin')->check() && Auth::guard('admin')->user()->role === 'admin') {
            return $next($request);
        }

        Log::warning('Попытка доступа к защищённому маршруту без авторизации', [
            'ip' => $request->ip(),
            'url' => $request->fullUrl(),
        ]);

        return abort(404);
    }
}

