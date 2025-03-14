<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\Message;

class DeleteOldMessages extends Command
{
    protected $signature = 'messages:purge';
    protected $description = 'Удаление сообщений, удаленных более недели назад';

    public function handle()
    {
        $deletedMessages = Message::onlyTrashed()
            ->where('deleted_at', '<', Carbon::now()->subWeek())
            ->forceDelete(); // Используем forceDelete() вместо delete()

        $this->info("Удалено {$deletedMessages} сообщений.");
    }
}
