<?php

namespace App\Listeners;

use App\Events\AddressUpdated;
use App\Services\OpenpayService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\App;

class OpenpayAddressUpdated
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param AddressUpdated $event
     * @return void
     */
    public function handle(AddressUpdated $event)
    {
        App::make(OpenpayService::class)->updateAddress($event->user);
    }
}
