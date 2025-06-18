<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdatePasswordRequest;
use App\Services\AccountService;
use Illuminate\View\View;

class AccountController extends Controller
{
    protected $accountService;

    public function __construct(AccountService $accountService)
    {
        $this->accountService = $accountService;
    }

    public function index($type, $id): View|\Illuminate\Http\RedirectResponse
    {
        $user = $this->accountService->getUserByTypeAndId($type, $id);

        if (!$user) {
            return redirect()->route('CoursesIndex')->with('error', 'Пользователь не найден.');
        }

        return view('show.profile', compact('user'));
    }

    public function updatePassword(UpdatePasswordRequest $request, $type, $id)
    {
        $user = $this->accountService->getUserByTypeAndId($type, $id);

        if (!$user) {
            return redirect()->route('CoursesIndex')->with('error', 'Пользователь не найден.');
        }

        try {
            $this->accountService->updatePassword(
                $user,
                $request->input('password'),
                $request->input('new_password'),
            );
        } catch (\App\Exceptions\InvalidCurrentPasswordException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->back()->with('success', 'Пароль успешно обновлён.');
    }
}
