<?php

namespace App\Providers;

use App\Events\AddressUpdated;
use App\Listeners\CustomerDeletedFromAdmin;
use App\Listeners\OpenpayAddressUpdated;
use App\Listeners\OpenpayUserCreation;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use TCG\Voyager\Events\BreadDataDeleted;

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
