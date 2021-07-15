<?php

namespace App\Console\Commands;

use App\Sat\Importer\Types\CFDI;
use App\Sat\SatWSRequestImporter;
use App\Sat\SatWSRequestStatusUpdater;
use Illuminate\Console\Command;

class ImportCfdisRequest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sat:import
                            {--status : Checa el status }
                            {--reset-failed : Resetea los ImportFailed a Downloaded para procesarlos nuevamente.}
                            {--reset-download= : Resetea los importados a status de Downloaded para su reprocesamiento. }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Importa todos los CFDI/Metadata descargados del servicio web del SAT.';

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
        if($this->option('reset-failed')) {
            SatWSRequestStatusUpdater::resetFailedImport();
            return 0;
        }

        if($this->option('reset-download') != null) {
            $model = config('sat.modelName');
            SatWSRequestStatusUpdater::setDownloaded($model::findOrFail($this->option('reset-download')));
            return 0;
        }

        SatWSRequestImporter::import(new CFDI())->all();


        return 0;
    }
}
