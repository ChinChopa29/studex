<?php

namespace App\Http\Requests;

use App\Services\AccountService;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        $type = $this->route('type');
        $id   = $this->route('id');

        $user = app(AccountService::class)->getUserByTypeAndId($type, $id);
        return $user && $user->id === auth()->id();
    }
    public function rules(): array
    {
        return [
            'password' => ['required'],
            'new_password' => ['required', 'min:5', 'confirmed'],
        ];
    }
}
