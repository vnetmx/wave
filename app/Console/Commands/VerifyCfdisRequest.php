<?php

namespace App\Console\Commands;

use App\Exceptions\SatWsRequestFailedException;
use App\Exceptions\SatWsRequestRejectedException;
use App\Sat\SatWSRequestChecker;
use App\Sat\SatWSRequestDownloader;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class VerifyCfdisRequest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sat:verify';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verifica todas las solicitudes registradas en la base de datos y si han sido finalizadas, las descarga';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
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
                $this->error($e->getMessage());
            }
        });
        return 0;
    }
}
