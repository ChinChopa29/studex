<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class IsTeacher
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $teacher = Auth::guard('teacher')->user();
        $admin = Auth::guard('admin')->user();

        if ($teacher || $admin) {
            return $next($request);
        }

        Log::warning('Попытка доступа к защищённому маршруту без авторизации', [
            'ip' => $request->ip(),
            'url' => $request->fullUrl(),
        ]);

        return abort(404);
    }
}
