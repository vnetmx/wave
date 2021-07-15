<?php

namespace App\Console\Commands;

use App\Services\SatWSService;
use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;
use Illuminate\Console\Command;

class ObtainCfdis extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sat:request
                    {--issued : Obtiene los CFDI/Metadata Emitidos (Default) }
                    {--received : Obtiene los CFDI/Metadata Recibidos}
                    {--cfdis : Obtiene CFDIs (Default)}
                    {--metadata : Obtiene la Metadata}
                    {--from= : Fecha "Y-m-d hh:mm:ss" desde la cual se obtiene la solicitud y si no se especifica se usa la fecha del día actual}
                    {--to= : Fecha "Y-m-d hh:mm:ss" desde la cual se obtiene la solicitud y si no se especifica se usa la fecha del día actual}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Realizar una petición para la descarga de CFDIs al WebService del SAT';

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
        $downloadType = $this->option('received') ? 'received':'issued';
        $requestType = $this->option('cfdis') ? 'cfdis':'metadata';

        // Si no se espescifica from y to, entonces procedemos a el ultimo mes, por simplicidad, usaremos el del 2000
        $from = $this->option('from') ?? Carbon::today();
        $to = $this->option('to') ?? Carbon::today()->hour(23)->minute(59)->second(59);

        try {
            // Test el formato de las fechas.
            $from = $from instanceof Carbon ? $from : Carbon::createFromFormat('Y-m-d H:i:s', $from);
            $to = $to instanceof Carbon ? $to : Carbon::createFromFormat('Y-m-d H:i:s', $to);

        } catch(InvalidFormatException $e)
        {
            $this->error('El formato de FROM o TO es invalido.');
            return 1;
        }

        $result = SatWSService::getInstance()
            ->$downloadType()
            ->$requestType()
            ->from($from)
            ->to($to)
            ->get();

        $this->info($result);

        return 0;
    }
}
