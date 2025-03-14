<?php

namespace App\Providers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Log;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void {
        View::composer('*', function ($view) {
            $user = null;
            $userType = null;
            $unreadMessagesCount = 0; // ✅ Объявляем заранее, чтобы избежать ошибки
    
            if (Auth::guard('student')->check()) {
                $user = Auth::guard('student')->user();
                $userType = \App\Models\Student::class;
            } elseif (Auth::guard('teacher')->check()) {
                $user = Auth::guard('teacher')->user();
                $userType = \App\Models\Teacher::class;
            } elseif (Auth::guard('admin')->check()) {
                $user = Auth::guard('admin')->user();
                $userType = \App\Models\User::class;
            }
    
            if ($user) {
                $receiverType = match (true) {
                    $user instanceof \App\Models\Student => \App\Models\Student::class,
                    $user instanceof \App\Models\Teacher => \App\Models\Teacher::class,
                    $user instanceof \App\Models\User => \App\Models\User::class, 
                    default => null,
                };
            
                $unreadMessagesCount = Message::where('receiver_id', $user->id)
                    ->where('receiver_type', $receiverType)
                    ->where('status', 0)
                    ->count(); // ✅ Считаем количество вместо get()
            }
    
            $view->with('unreadMessagesCount', $unreadMessagesCount);
        });
            Route::middleware('web')
            ->prefix('admin')
            ->name('admin.')
            ->group(base_path('routes/admin.php'));
        }

}
