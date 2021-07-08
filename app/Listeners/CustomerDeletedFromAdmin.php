<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use TCG\Voyager\Events\BreadDataDeleted;

class CustomerDeletedFromAdmin
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
     * @param  BreadDataDeleted  $event
     * @return void
     */
    public function handle(BreadDataDeleted $event)
    {
        //
    }
}
