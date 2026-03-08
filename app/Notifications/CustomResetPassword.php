<?php

namespace App\Notifications;

use App\Services\MailSettingsService;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Lang;

class CustomResetPassword extends ResetPassword
{
    public function __construct(string $token, private ?MailSettingsService $mailSettings = null)
    {
        parent::__construct($token);

        $this->mailSettings = $this->mailSettings ?? app(MailSettingsService::class);
    }

    public function toMail($notifiable)
    {
        $mail = (new MailMessage)
            ->subject(Lang::get('Reset Password Notification'))
            ->line(Lang::get('You are receiving this email because we received a password reset request for your account.'))
            ->action(Lang::get('Reset Password'), $this->resetUrl($notifiable))
            ->line(Lang::get('This password reset link will expire in :count minutes.', [
                'count' => config('auth.passwords.'.config('auth.defaults.passwords').'.expire'),
            ]))
            ->line(Lang::get('If you did not request a password reset, no further action is required.'));

        if ($this->mailSettings->isActive()) {
            $this->mailSettings->applyConfig();

            $mailer = $this->mailSettings->getVerificationMailer();

            if ($mailer) {
                $mail->mailer($mailer);

                [$fromAddress, $fromName] = $this->mailSettings->getFrom($mailer);

                if ($fromAddress) {
                    $mail->from($fromAddress, $fromName);
                }
            }
        }

        return $mail;
    }
}
