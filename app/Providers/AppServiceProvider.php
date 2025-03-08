<?php

namespace App\Providers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
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
    public function boot(): void
    {
        View::composer('*', function ($view) {
            $user = null;
    
            if (Auth::guard('student')->check()) {
                $user = Auth::guard('student')->user();
            } elseif (Auth::guard('teacher')->check()) {
                $user = Auth::guard('teacher')->user();
            } elseif (Auth::guard('admin')->check()) {
                $user = Auth::guard('admin')->user();
            }
    
            if ($user) {
                $unreadMessagesCount = Message::where('receiver_id', $user->id)
                    ->where('status', 0)
                    ->count();
            } else {
                $unreadMessagesCount = 0; 
            }
    
            $view->with('unreadMessagesCount', $unreadMessagesCount);
        });

        // Подключаем маршруты админки
        Route::middleware('web')
            ->prefix('admin')
            ->name('admin.')
            ->group(base_path('routes/admin.php'));
    }

    
}
