<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Laravel\Socialite\Contracts\User as SocialiteUser;
use SocialiteProviders\Manager\SocialiteWasCalled;
use SocialiteProviders\Microsoft\MicrosoftExtendSocialite;

class MicrosoftAuthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->bootMicrosoftSocialite();
    }

    protected function bootMicrosoftSocialite()
    {
        $socialite = $this->app->make('Laravel\Socialite\Contracts\Factory');
        
        $socialite->extend('microsoft', function ($app) use ($socialite) {
            $config = $app['config']['services.microsoft'];
            
            return $socialite->buildProvider(MicrosoftExtendSocialite::class, $config)
                ->setConfig($config);
        });
    }
}
