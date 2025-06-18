<?php

namespace App\Services;

use App\Exceptions\InvalidCurrentPasswordException;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class AccountService
{
    public function getUserByTypeAndId($type, $id): ?Model
    {
        return match($type) {
            'student' => Student::with('groups')->find($id),
            'teacher' => Teacher::find($id),
            default => null,
        };
    }

    public function updatePassword(Model $user, string $currentPassword, string $newPassword): void
    {
        if (!Hash::check($currentPassword, $user->password)) {
            throw new InvalidCurrentPasswordException('Неверный текущий пароль.');
        }

        $user->password = Hash::make($newPassword);
        $user->save();
    }
}
