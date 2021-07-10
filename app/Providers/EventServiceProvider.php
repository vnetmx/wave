<?php

namespace App\Providers;

use App\Events\AddressUpdated;
use App\Events\UserUpdated;
use App\Listeners\OpenpayAddressUpdated;
use App\Listeners\OpenpayUserCreation;
use Illuminate\Auth\Events\Registered;
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
            OpenpayUserCreation::class
        ],
        AddressUpdated::class => [
            OpenpayAddressUpdated::class
        ],
        UserUpdated::class => [

        ],
        /*
        BreadDataDeleted::class => [
            CustomerDeletedFromAdmin::class
        ],
        */
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
