<?php

namespace App\Services;

use App\Models\MailSetting;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Throwable;

class MailSettingsService
{
    public function getSettings(): ?MailSetting
    {
        if (!Schema::hasTable('mail_settings')) {
            return null;
        }

        return MailSetting::first();
    }

    public function isActive(): bool
    {
        return (bool) ($this->getSettings()?->is_active);
    }

    public function applyConfig(bool $force = false): void
    {
        $settings = $this->getSettings();
        if (!$settings || (!$settings->is_active && !$force)) {
            return;
        }

        config([
            'mail.default' => 'smtp_primary',
            'mail.mailers.smtp_primary.transport' => 'smtp',
            'mail.mailers.smtp_primary.host' => $settings->primary_host,
            'mail.mailers.smtp_primary.port' => $settings->primary_port,
            'mail.mailers.smtp_primary.username' => $settings->primary_username,
            'mail.mailers.smtp_primary.password' => $this->decrypt($settings->primary_password_encrypted),
            'mail.mailers.smtp_primary.encryption' => $settings->primary_encryption,

            'mail.mailers.smtp_secondary.transport' => 'smtp',
            'mail.mailers.smtp_secondary.host' => $settings->secondary_host,
            'mail.mailers.smtp_secondary.port' => $settings->secondary_port,
            'mail.mailers.smtp_secondary.username' => $settings->secondary_username,
            'mail.mailers.smtp_secondary.password' => $this->decrypt($settings->secondary_password_encrypted),
            'mail.mailers.smtp_secondary.encryption' => $settings->secondary_encryption,
        ]);
    }

    public function getVerificationMailer(): ?string
    {
        $settings = $this->getSettings();
        if (!$settings || !$settings->is_active) {
            return null;
        }

        return $settings->verification_mailer === 'secondary' ? 'smtp_secondary' : 'smtp_primary';
    }

    public function getNotificationMailer(): ?string
    {
        $settings = $this->getSettings();
        if (!$settings || !$settings->is_active) {
            return null;
        }

        return $settings->notification_mailer === 'secondary' ? 'smtp_secondary' : 'smtp_primary';
    }

    public function getFrom(string $mailer): array
    {
        $settings = $this->getSettings();
        if (!$settings) {
            return [null, null];
        }

        if ($mailer === 'smtp_secondary') {
            return [$settings->secondary_from_address, $settings->secondary_from_name];
        }

        return [$settings->primary_from_address, $settings->primary_from_name];
    }

    public function getAdminNotifyEmails(): array
    {
        $settings = $this->getSettings();
        if (!$settings || empty($settings->admin_notify_emails)) {
            return [];
        }

        return collect(explode(',', $settings->admin_notify_emails))
            ->map(fn ($email) => trim($email))
            ->filter()
            ->values()
            ->all();
    }

    public function sendNotificationEmail(string|array $to, string $subject, string $message): bool
    {
        $mailer = $this->getNotificationMailer();
        if (!$mailer) {
            return false;
        }

        $this->applyConfig();
        [$fromAddress, $fromName] = $this->getFrom($mailer);

        try {
            Mail::mailer($mailer)
                ->to($to)
                ->send(new \App\Mail\SimpleNotificationMail($subject, $message, $fromAddress, $fromName));
            return true;
        } catch (Throwable $e) {
            report($e);
            return false;
        }
    }

    public function sendVerificationEmail(string $to, string $subject, string $body): bool
    {
        $mailer = $this->getVerificationMailer();
        if (!$mailer) {
            return false;
        }

        $this->applyConfig();
        [$fromAddress, $fromName] = $this->getFrom($mailer);

        try {
            Mail::mailer($mailer)
                ->to($to)
                ->send(new \App\Mail\SimpleNotificationMail($subject, $body, $fromAddress, $fromName));
            return true;
        } catch (Throwable $e) {
            report($e);
            return false;
        }
    }

    public function encrypt(?string $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        return Crypt::encryptString($value);
    }

    public function decrypt(?string $value): ?string
    {
        if (!$value) {
            return null;
        }

        try {
            return Crypt::decryptString($value);
        } catch (Throwable $e) {
            report($e);
            return null;
        }
    }
}
