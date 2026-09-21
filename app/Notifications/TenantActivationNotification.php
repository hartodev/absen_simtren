<?php

namespace App\Notifications;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class TenantActivationNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Tenant $tenant,
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
                'tenant' => $this->tenant->id,
                'user' => $this->user->id,
            ]
        );

        return (new MailMessage)
            ->subject('Aktivasi Akun Lembaga: ' . $this->tenant->nama_lembaga)
            ->greeting('Halo, ' . $this->user->name . '!')
            ->line('Terima kasih telah mendaftarkan "' . $this->tenant->nama_lembaga . '" di Smart Absen.')
            ->line('Silakan klik tombol di bawah untuk mengaktifkan akun dan lembaga Anda. Link ini berlaku selama 24 jam.')
            ->action('Aktivasi Akun Saya', $url)
            ->line('Jika Anda tidak merasa mendaftar, abaikan email ini.');
    }
}
