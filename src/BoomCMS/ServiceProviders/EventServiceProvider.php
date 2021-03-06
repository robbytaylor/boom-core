<?php

namespace BoomCMS\ServiceProviders;

use BoomCMS\Events;
use BoomCMS\Listeners;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        Events\AccountCreated::class => [
            Listeners\SendAccountCreatedNotification::class,
        ],
        Events\Auth\PasswordChanged::class => [
            Listeners\SendPasswordChangedNotification::class,
        ],
        BoomCMS\Events\Auth\SuccessfulLogin::class => [
            Listeners\ResetFailedLogins::class,
        ],
        Events\PageSearchSettingsWereUpdated::class => [
            Listeners\UpdateSearchText::class,
        ],
        Events\PageTitleWasChanged::class => [
            Listeners\UpdatePagePrimaryURLToTitle::class,
        ],
        Events\PageWasDeleted::class => [
            Listeners\RemovePageFromSearch::class,
        ],
        Events\PageWasPublished::class => [
            Listeners\SaveSearchText::class,
            Listeners\RemoveExpiredSearchTexts::class,
        ],
        Events\PageWasEmbargoed::class => [
            Listeners\SaveSearchText::class,
        ],
    ];
}
