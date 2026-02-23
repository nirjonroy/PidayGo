<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MailSetting;
use App\Services\MailSettingsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class MailSettingsController extends Controller
{
    public function index(): View
    {
        return view('admin.mail-settings.index', [
            'settings' => MailSetting::first() ?? new MailSetting(),
        ]);
    }

    public function update(Request $request, MailSettingsService $service): RedirectResponse
    {
        $data = $this->validatePayload($request);

        $settings = MailSetting::first() ?? new MailSetting();

        $settings->fill($data);
        $settings->updated_by = $request->user('admin')->id;

        if ($request->filled('primary_password')) {
            $settings->primary_password_encrypted = $service->encrypt($request->input('primary_password'));
        }
        if ($request->filled('secondary_password')) {
            $settings->secondary_password_encrypted = $service->encrypt($request->input('secondary_password'));
        }

        $settings->save();

        return back()->with('status', 'Mail settings saved.');
    }

    public function test(Request $request, MailSettingsService $service): RedirectResponse
    {
        $validated = Validator::make($request->all(), [
            'profile' => ['required', 'in:primary,secondary'],
            'email' => ['required', 'email'],
        ])->validate();

        $service->applyConfig(true);
        $mailer = $validated['profile'] === 'secondary' ? 'smtp_secondary' : 'smtp_primary';
        [$fromAddress, $fromName] = $service->getFrom($mailer);

        try {
            \Mail::mailer($mailer)
                ->to($validated['email'])
                ->send(new \App\Mail\SimpleNotificationMail('Test Email', 'This is a test email.', $fromAddress, $fromName));

            return back()->with('status', 'Test email sent.');
        } catch (\Throwable $e) {
            report($e);
            return back()->withErrors(['email' => 'Failed to send test email.']);
        }
    }

    private function validatePayload(Request $request): array
    {
        $validated = $request->validate([
            'is_active' => ['nullable', 'boolean'],

            'primary_host' => ['nullable', 'string', 'max:255'],
            'primary_port' => ['nullable', 'integer', 'min:1', 'max:65535'],
            'primary_username' => ['nullable', 'string', 'max:255'],
            'primary_encryption' => ['nullable', 'string', 'max:20'],
            'primary_from_address' => ['nullable', 'email', 'max:255'],
            'primary_from_name' => ['nullable', 'string', 'max:255'],

            'secondary_host' => ['nullable', 'string', 'max:255'],
            'secondary_port' => ['nullable', 'integer', 'min:1', 'max:65535'],
            'secondary_username' => ['nullable', 'string', 'max:255'],
            'secondary_encryption' => ['nullable', 'string', 'max:20'],
            'secondary_from_address' => ['nullable', 'email', 'max:255'],
            'secondary_from_name' => ['nullable', 'string', 'max:255'],

            'verification_mailer' => ['required', 'in:primary,secondary'],
            'notification_mailer' => ['required', 'in:primary,secondary'],
            'admin_notify_emails' => ['nullable', 'string'],
        ]);

        $validated['is_active'] = (bool) ($validated['is_active'] ?? false);

        if ($validated['is_active']) {
            $this->validateActiveProfile($validated, 'verification_mailer');
            $this->validateActiveProfile($validated, 'notification_mailer');
        }

        return $validated;
    }

    private function validateActiveProfile(array $validated, string $field): void
    {
        $profile = $validated[$field] ?? 'primary';
        $prefix = $profile === 'secondary' ? 'secondary' : 'primary';

        $required = [
            $prefix . '_host',
            $prefix . '_port',
            $prefix . '_from_address',
        ];

        foreach ($required as $key) {
            if (empty($validated[$key])) {
                abort(422, 'Selected mailer profile is incomplete.');
            }
        }
    }
}
