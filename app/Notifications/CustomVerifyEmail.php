<?php

namespace App\Notifications;

use App\Services\MailSettingsService;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

class CustomVerifyEmail extends VerifyEmail
{
    public function __construct(private ?MailSettingsService $mailSettings = null)
    {
        $this->mailSettings = $this->mailSettings ?? app(MailSettingsService::class);
    }

    public function via($notifiable)
    {
        if (!$this->mailSettings->isActive()) {
            return [];
        }

        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $mailSettings = $this->mailSettings;
        $mailSettings->applyConfig();
        $mailer = $mailSettings->getVerificationMailer();
        $verificationUrl = $this->verificationUrl($notifiable);

        $mail = (new MailMessage)
            ->subject('Verify Email Address')
            ->line('Please click the button below to verify your email address.')
            ->action('Verify Email Address', $verificationUrl)
            ->line('If you did not create an account, no further action is required.');

        if ($mailer) {
            $mail->mailer($mailer);
            [$fromAddress, $fromName] = $mailSettings->getFrom($mailer);
            if ($fromAddress) {
                $mail->from($fromAddress, $fromName);
            }
        }

        return $mail;
    }
}
