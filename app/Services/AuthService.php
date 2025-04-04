<?php
namespace App\Services;

use App\Models\Message;
use App\Models\User;
use App\Models\Teacher;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthService
{
   public function checkAuth() 
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

      return null;  
   }

    public function userAuthenticate($email, $password)
    {
      if ($admin = User::where('email', $email)->first()) {
         return $this->checkPasswordAndLogin($admin, $password, 'admin');
      }

      if ($teacher = Teacher::where('email', $email)->first()) {
         return $this->checkPasswordAndLogin($teacher, $password, 'teacher');
      }

      if ($student = Student::where('email', $email)->first()) {
         return $this->checkPasswordAndLogin($student, $password, 'student');
      }

      return false;
    }

   public function authenticateAndGetRedirect($email, $password)
   {
      $authResult = $this->userAuthenticate($email, $password);

      if ($authResult === 'admin') {
         return redirect()->route('admin.index');
      }

      if (in_array($authResult, ['teacher', 'student'])) {
         $messages = Message::all();
         return redirect()->route('CoursesIndex', compact('messages'));
      }

      return redirect()->route('login')->withErrors([
         'login' => 'Неверная почта или пароль'
      ]);
   }

   private function checkPasswordAndLogin($user, $password, $guard)
   {
      if (Hash::check($password, $user->password)) {
         auth()->guard($guard)->login($user);
         request()->session()->regenerate();
         return $guard;
      }
      return false;
   }

   public function logout()
   {
      Auth::logout();
      session()->invalidate();
      session()->regenerateToken();
   }
}
