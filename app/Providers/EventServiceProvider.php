<?php

namespace App\Providers;

use App\Listeners\DeleteOldTokens;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Registered;
use App\Listeners\DeleteOldRefreshTokens;
use Laravel\Passport\Events\AccessTokenCreated;
use Laravel\Passport\Events\RefreshTokenCreated;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        AccessTokenCreated::class => [
            DeleteOldTokens::class,
        ],
        RefreshTokenCreated::class => [
            DeleteOldRefreshTokens::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
