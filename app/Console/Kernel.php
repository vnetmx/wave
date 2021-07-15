<?php

namespace App\Console;

use App\Exceptions\SatWsRequestFailedException;
use App\Exceptions\SatWsRequestRejectedException;
use App\Sat\Importer\Types\CFDI;
use App\Sat\SatWSRequestChecker;
use App\Sat\SatWSRequestDownloader;
use App\Sat\SatWSRequestImporter;
use App\Services\SatWSService;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        /**
         * Solicitamos todas los CFDI's de hoy.
         */
        $schedule->call(function()
        {
            SatWSService::getInstance()
                ->cfdis()
                ->received()
                ->today()
                ->get();

        })->dailyAt('22:00')->then(function ()
        {
            SatWSService::getInstance()
                ->cfdis()
                ->issued()
                ->today()
                ->get();
        });

        /**
         * Revisamos todas nuestras peticiones cada
         * 15 minutos por cambios.
         */
        $schedule->call(function()
        {
            SatWSRequestChecker::all()->each(/**
             * @param SatWSRequestChecker $item
             * @param $key
             * @throws SatWsRequestFailedException
             * @throws SatWsRequestRejectedException
             */ function($item, $key)
            {
                try {
                    $checked = $item->verify();
                    if ($checked->isFinished()) {
                        (new SatWSRequestDownloader($checked))->download();
                    }
                } catch(SatWsRequestFailedException | SatWsRequestRejectedException $e)
                {
                    Log::debug($e->getMessage());
                }
            });

        })->everyFifteenMinutes()->then(function ()
        {
            /**
             * Cada que revisamos las peticiones importamos lo
             * que este descargado de CFDIs
             */
            SatWSRequestImporter::import(new CFDI())->all();
        });

        /**
         * Vamos a comprobar
         */

    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
