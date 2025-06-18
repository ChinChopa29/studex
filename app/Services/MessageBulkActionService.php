<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Models\Message;
use Illuminate\Support\Collection;

class MessageBulkActionService
{
    public function markAsRead(Collection|array $messageIds, $user)
    {
        Message::whereIn('id', $messageIds)
            ->where('receiver_id', $user->id)
            ->update(['status' => 1]);
    }

    public function softDelete(Collection|array $messageIds, $user)
    {
        Message::whereIn('id', $messageIds)
            ->where(function ($query) use ($user) {
                $query->where('receiver_id', $user->id)
                      ->orWhere('sender_id', $user->id);
            })
            ->update([
                'status' => DB::raw("CASE WHEN receiver_id = {$user->id} AND status = 0 THEN 1 ELSE status END"),
                'deleted_by_receiver_at' => DB::raw("CASE WHEN receiver_id = {$user->id} THEN NOW() ELSE deleted_by_receiver_at END"),
                'deleted_by_sender_at' => DB::raw("CASE WHEN sender_id = {$user->id} THEN NOW() ELSE deleted_by_sender_at END"),
            ]);

        DB::table('favorite_messages')->whereIn('message_id', $messageIds)->delete();
    }

    public function forceDelete(Collection|array $messageIds, $user)
    {
        Message::whereIn('id', $messageIds)
            ->where(function ($query) use ($user) {
                $query->where('receiver_id', $user->id)
                      ->orWhere('sender_id', $user->id);
            })
            ->update([
                'deleted_by_receiver' => DB::raw("CASE WHEN receiver_id = {$user->id} THEN true ELSE deleted_by_receiver END"),
                'deleted_by_sender' => DB::raw("CASE WHEN sender_id = {$user->id} THEN true ELSE deleted_by_sender END"),
            ]);
    }

    public function favorite(Collection|array $messageIds, $user)
    {
        $favorites = collect($messageIds)->map(function ($id) use ($user) {
            return [
                'user_id' => $user->id,
                'user_type' => get_class($user),
                'message_id' => $id,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        })->toArray();

        DB::table('favorite_messages')->insertOrIgnore($favorites);
    }

    public function unfavorite(Collection|array $messageIds, $user)
    {
        DB::table('favorite_messages')
            ->where('user_id', $user->id)
            ->where('user_type', get_class($user))
            ->whereIn('message_id', $messageIds)
            ->delete();
    }

    public function handle(string $action, array $messageIds, $user)
    {
        match ($action) {
            'read' => $this->markAsRead($messageIds, $user),
            'delete' => $this->softDelete($messageIds, $user),
            'forceDelete' => $this->forceDelete($messageIds, $user),
            'favorite' => $this->favorite($messageIds, $user),
            'unfavorite' => $this->unfavorite($messageIds, $user),
            default => throw new \InvalidArgumentException("Неизвестное действие: $action"),
        };
    }
}
