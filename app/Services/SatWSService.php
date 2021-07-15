<?php


namespace App\Services;


use App\Exceptions\SatWsRequestExpiredException;
use App\Exceptions\SatWsRequestFailedException;
use App\Exceptions\SatWsRequestRejectedException;
use App\Sat\SatWSRequestStatusUpdater;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use PhpCfdi\SatWsDescargaMasiva\RequestBuilder\FielRequestBuilder\Fiel;
use PhpCfdi\SatWsDescargaMasiva\RequestBuilder\FielRequestBuilder\FielRequestBuilder;
use PhpCfdi\SatWsDescargaMasiva\Service;
use PhpCfdi\SatWsDescargaMasiva\Services\Query\QueryParameters;
use PhpCfdi\SatWsDescargaMasiva\Shared\DateTimePeriod;
use PhpCfdi\SatWsDescargaMasiva\Shared\DownloadType;
use PhpCfdi\SatWsDescargaMasiva\Shared\RequestType;
use PhpCfdi\SatWsDescargaMasiva\Shared\ServiceEndpoints;
use PhpCfdi\SatWsDescargaMasiva\WebClient\GuzzleWebClient;

class SatWSService
{
    public $service;
    public $downloadType = null;
    public $requestType = null;
    public $queryFrom;
    public $queryTo;
    public $filterRfc = '';
    public $modelName;
    public $getRetenciones;
    protected $statusRequest;
    protected $status;

    /**
     * Inicializa un objeto de tipo SatWSService
     *
     * @param bool $retenciones
     * @return SatWSService
     * @throws \Exception
     */
    public static function getInstance($retenciones = false): SatWSService
    {
        $func = 'config';
        if(config('sat.useEnv') == false && function_exists('setting')) {
            $func = 'setting';
        }
        $certificate = $func('sat.fiel_certificate');
        $privatekey = $func('sat.fiel_privatekey');
        $passphrase = $func('sat.fiel_passphrase');

        return new static($certificate, $privatekey, $passphrase, null, $retenciones);
    }

    public function __construct($certificate, $privateKey, $passphrase, $modelName = null, $retenciones = false)
    {
        if( ! file_exists($certificate) || ! file_exists($privateKey))
        {
            throw new \Exception('Archivos de Certificado y/o Llave no encontrados.');
        }
        $fiel = Fiel::create(file_get_contents($certificate), file_get_contents($privateKey), $passphrase);
        if (!$fiel->isValid()) {
            throw new \Exception('FIEL no valida');
        }

        // Creación del servicio
        $this->service = new Service(
                                new FielRequestBuilder($fiel),
                                new GuzzleWebClient(),
                                null,
                                $retenciones ?  ServiceEndpoints::retenciones() : null);

        $this->modelName = !is_null($modelName) ? $modelName : config('sat.modelName');
        $this->getRetenciones = $retenciones;
        return $this;
    }

    public function service(): Service
    {
        return $this->service;
    }
    public function api(): Service
    {
        return $this->service;
    }

    public function cfdis(): SatWSService
    {
        $this->requestType = RequestType::cfdi();
        return $this;
    }

    public function metadata(): SatWSService
    {
        $this->requestType = RequestType::metadata();
        return $this;
    }

    public function issued(): SatWSService
    {
        $this->downloadType = DownloadType::issued();
        return $this;
    }

    public function received(): SatWSService
    {
        $this->downloadType = DownloadType::received();
        return $this;
    }

    public function from(Carbon $date): SatWSService
    {
        $this->queryFrom = $date->toDateTimeString();
        return $this;
    }

    public function to(Carbon $date): SatWSService
    {
        $this->queryTo = $date->toDateTimeString();
        return $this;
    }

    public function rfc($rfc)
    {
        $this->filterRfc = $rfc;
        return $this;
    }

    public function today()
    {
        $this->queryFrom = Carbon::today()->toDateTimeString();
        $this->queryTo = Carbon::today()->hour(23)->minute(59)->second(59)->toDateTimeString();
        return $this;
    }

    public function yesterday()
    {
        $this->queryFrom = Carbon::yesterday()->toDateTimeString();
        $this->queryTo = Carbon::yesterday()->hour(23)->minute(59)->second(59)->toDateTimeString();
        return $this;
    }

    public function thisMonth()
    {
        $this->queryFrom = Carbon::now()->firstOfMonth()->toDateTimeString();
        $this->queryTo = Carbon::now()->toDateTimeString();
        return $this;
    }

    public function lastMonth()
    {
        $this->queryFrom = Carbon::now()->subMonth()->firstOfMonth()->toDateTimeString();
        $this->queryTo = Carbon::now()->subMonth()->lastOfMonth()->toDateTimeString();
        return $this;
    }

    public function thisYear()
    {
        $this->queryFrom = Carbon::now()->firstOfYear()->toDateTimeString();
        $this->queryTo = Carbon::now()->toDateTimeString();
        return $this;
    }

    public function lastYear()
    {
        $this->queryFrom = Carbon::now()->subYear()->firstOfYear()->toDateTimeString();
        $this->queryTo = Carbon::now()->subYear()->lastOfYear()->toDateTimeString();
        return $this;
    }

    protected function buildQuery()
    {
        return QueryParameters::create(
            DateTimePeriod::createFromValues($this->queryFrom, $this->queryTo),
            $this->downloadType,
            $this->requestType,
            $this->filterRfc
        );
    }

    public function get(): string
    {
        $query = $this->service->query($this->buildQuery());
        // verificar que el proceso de consulta fue correcto
        if (! $query->getStatus()->isAccepted()) {
            throw new SatWsRequestFailedException("Fallo al presentar la consulta: {$query->getStatus()->getMessage()}");
        }

        SatWSRequestStatusUpdater::create([
            'request_id' => $query->getRequestId(),
            'status' => 'Accepted',
            'downloadType' => (string)$this->downloadType,
            'requestType' => (string)$this->requestType,
            'from' => $this->queryFrom,
            'to' => $this->queryTo
        ]);

        return $query->getRequestId();
    }
}
