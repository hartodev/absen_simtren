<?php

namespace App\Notifications;

use App\Models\Company;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class CompanyActivationNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Company $company,
        public User $user,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = URL::temporarySignedRoute(
            'register.activate',
            now()->addHours(24),
            [
                'company' => $this->company->id,
                'user' => $this->user->id,
            ]
        );

        return (new MailMessage)
            ->subject('Aktivasi Akun Organisasi: ' . $this->company->name)
            ->greeting('Halo, ' . $this->user->name . '!')
            ->line('Terima kasih telah mendaftarkan "' . $this->company->name . '" di sistem kami.')
            ->line('Silakan klik tombol di bawah untuk mengaktifkan akun dan organisasi Anda. Link ini berlaku selama 24 jam.')
            ->action('Aktivasi Akun Saya', $url)
            ->line('Jika Anda tidak merasa mendaftar, abaikan email ini.');
    }
}
