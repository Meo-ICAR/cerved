<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Socialite\Contracts\Factory as SocialiteFactory;

class MicrosoftAuthServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->make(SocialiteFactory::class)
            ->extend('microsoft', function ($app) {
                $config = $app['config']['services.microsoft'];
                return new \SocialiteProviders\Microsoft\MicrosoftProvider(
                    $app['request'],
                    $config['client_id'],
                    $config['client_secret'],
                    $config['redirect'],
                    $config
                );
            });
    }

    public function boot(): void
    {
        //
    }
}
