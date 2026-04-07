<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
            $this->clearStaleViewCacheWhenViteManifestIsMissing();
        }

        $this->configureAuthMailNotifications();
    }

    private function clearStaleViewCacheWhenViteManifestIsMissing(): void
    {
        if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot'))) {
            return;
        }

        $marker = storage_path('framework/vite-manifest-missing-view-cache-cleared');

        if (file_exists($marker)) {
            return;
        }

        Artisan::call('view:clear');
        @file_put_contents($marker, now()->toIso8601String());
    }

    private function configureAuthMailNotifications(): void
    {
        $contactLine = 'For any suggestions, contact the developer at prashantchhatri2025@gmail.com.';

        ResetPassword::toMailUsing(function (object $notifiable, string $token) use ($contactLine) {
            $resetUrl = url(route('password.reset', [
                'token' => $token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ], false));

            return (new MailMessage)
                ->subject('KeepTheStreak | Reset Password')
                ->line(Lang::get('You are receiving this email because we received a password reset request for your account.'))
                ->action(Lang::get('Reset Password'), $resetUrl)
                ->line(Lang::get(
                    'This password reset link will expire in :count minutes.',
                    ['count' => config('auth.passwords.'.config('auth.defaults.passwords').'.expire')]
                ))
                ->line(Lang::get('If you did not request a password reset, no further action is required.'))
                ->line($contactLine);
        });

        VerifyEmail::toMailUsing(function (object $notifiable, string $verificationUrl) use ($contactLine) {
            return (new MailMessage)
                ->subject('KeepTheStreak | Verify Email Address')
                ->line(Lang::get('Please click the button below to verify your email address.'))
                ->action(Lang::get('Verify Email Address'), $verificationUrl)
                ->line(Lang::get('If you did not create an account, no further action is required.'))
                ->line($contactLine);
        });
    }
}
