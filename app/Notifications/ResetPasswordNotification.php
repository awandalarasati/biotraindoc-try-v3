<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    public $token;
    public $email; // Tambahkan ini

    public function __construct($token, $email) // tambahkan parameter email
    {
        $this->token = $token;
        $this->email = $email; // Simpan email
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $resetUrl = url(route('password.reset', [
            'token' => $this->token,
            'email' => $this->email, // tambahkan email disini
        ], false));

        return (new MailMessage)
            ->subject('Reset Kata Sandi Anda - Biofarma')
            ->view('emails.reset-password', [
                'url' => $resetUrl,
                'name' => $notifiable->name,
            ]);
    }
}
