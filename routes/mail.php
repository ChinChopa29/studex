<?php

use App\Http\Controllers\MailController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:teacher,student,admin')->prefix('mail')->group(function () {
   Route::get('/send-form', [MailController::class, 'create'])->name('mailCreate');
   Route::post('/send', [MailController::class, 'store'])->name('mailStore');

   Route::get('/sended', [MailController::class, 'sended'])->name('mailSended');

   Route::get('/favorite', [MailController::class, 'favorite'])->name('mailFavorite');

   Route::get('/recent-deleted', [MailController::class, 'recentDeleted'])->name('mailRecentDeleted');

   Route::post('/messages/bulk-action', [MailController::class, 'bulkAction'])->name('mailBulkAction');

   Route::get('/search', [MailController::class, 'search'])->name('mailSearch');

   Route::get('/', [MailController::class, 'index'])->name('mailIndex');

   Route::get('{message}', [MailController::class, 'show'])->name('messageShow');

   Route::get('sended/{message}', [MailController::class, 'showSended'])->name('messageShowSended');

   Route::get('/deleted/{message}', [MailController::class, 'showDeleted'])->name('messageShowDeleted');

   Route::delete('{message}/delete', [MailController::class, 'destroy'])->name('messageDelete');

   Route::delete('{message}/force-delete', [MailController::class, 'forceDestroy'])->name('messageForceDelete');

   Route::get('/{message}/accept-invite', [MailController::class, 'acceptInvite'])->name('mailAcceptInvite');

   Route::get('/{message}/decline-invite', [MailController::class, 'declineInvite'])->name('mailDeclineInvite'); 
});

