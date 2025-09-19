<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends Notification
{
    /** @var string */
    private $url;

    /** @var string */
    private $brandName;

    /** @var string */
    private $brandLogo;

    public function __construct(string $url) {
        $this->url = $url;
        $this->brandName = 'thevoice.ma';
        $this->brandLogo = asset('/build/images/the-voice-logo.png');
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $broker   = config('auth.defaults.passwords');
        $minutes  = (int) config("auth.passwords.$broker.expire");

        return (new MailMessage)
            ->subject('Reset your password')
            ->markdown('emails.auth.reset-password', [
                'url'       => $this->url,
                'brandName' => $this->brandName,
                'brandLogo' => $this->brandLogo,
                'user'      => $notifiable,
                'linkExpirationDuration' => $minutes,
            ]);
    }
}
