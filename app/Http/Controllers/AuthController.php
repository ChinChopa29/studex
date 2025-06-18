<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthenticateRequest;
use App\Services\AuthService;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function login() 
    {
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.index');
        }
    
        if (Auth::guard('teacher')->check()) {
            return redirect()->route('CoursesIndex'); 
        }
    
        if (Auth::guard('student')->check()) {
            return redirect()->route('CoursesIndex'); 
        }
    
        return view('auth.auth');
    }

    public function authenticate(AuthenticateRequest $request)
    {
        $validated = $request->validated();

        return $this->authService->authenticateAndGetRedirect($validated['email'], $validated['password']);
    }

    public function logout()
    {
        $this->authService->logout();

        return redirect()->route('login');
    }
}
