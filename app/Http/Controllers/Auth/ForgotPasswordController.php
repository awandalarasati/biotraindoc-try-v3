<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Notifications\ResetPassword;
use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use Throwable;

class ForgotPasswordController extends Controller
{
    public function showLinkRequestForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return back()->withErrors(['email' => 'Email tidak ditemukan di sistem kami.']);
        }

        ResetPassword::toMailUsing(function ($notifiable, $token) use ($request) {
            return (new ResetPasswordNotification($token, $request->email))
                ->toMail($notifiable);
        });

        try {
            $status = Password::sendResetLink($request->only('email'));
        } catch (Throwable $e) {
            return back()->withErrors([
                'email' => 'Gagal mengirim email reset. Coba lagi nanti atau hubungi admin.'
            ]);
        }

        return $status === Password::RESET_LINK_SENT
            ? back()->with(['status' => __($status)])
            : back()->withErrors(['email' => __($status)]);
    }
}
