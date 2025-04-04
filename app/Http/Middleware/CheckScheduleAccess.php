<?php

namespace App\Http\Middleware;

use App\Models\Student;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckScheduleAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        $schedule = $request->route('schedule');
        
        if (Auth::guard('admin')->check()) {
            return $next($request);
        }

        if (Auth::guard('teacher')->check() && $schedule->teacher_id == Auth::id()) {
            return $next($request);
        }

        if (Auth::guard('student')->check()) {
            $student = Student::find(Auth::id());
            if ($schedule->group_id == $student->group_id) {
                return $next($request);
            }
        }

        abort(403);
    }
}
