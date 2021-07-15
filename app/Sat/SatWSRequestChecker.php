<?php


namespace App\Sat;


use App\Exceptions\SatWsRequestFailedException;
use App\Exceptions\SatWsRequestRejectedException;
use App\Services\SatWSService;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use PhpCfdi\SatWsDescargaMasiva\Service;


class SatWSRequestChecker
{
    protected $service;
    protected $requestId;

    /**
     * @var \PhpCfdi\SatWsDescargaMasiva\Shared\StatusRequest
     */
    protected $statusRequest;
    /**
     * @var \PhpCfdi\SatWsDescargaMasiva\Services\Verify\VerifyResult
     */
    protected $status;

    protected static $modelNameStatic;

    public static function setModelName($modelName)
    {
        static::$modelNameStatic = $modelName;
    }

    public static function all() : Collection
    {
        /**
         *         1 => ['name' => 'Accepted', 'message' => 'Aceptada'],
        2 => ['name' => 'InProgress', 'message' => 'En proceso'],
        3 => ['name' => 'Finished', 'message' => 'Terminada'],
        4 => ['name' => 'Failure', 'message' => 'Error'],
        5 => ['name' => 'Rejected', 'message' => 'Rechazada'],
        6 => ['name' => 'Expired', 'message' => 'Vencida'],
         * 8 => Downloaded
         */
        $failed = [
          'Failure',
          'Rejected',
          'Expired',
          'ImportFailed',
          'Downloaded',
          'Finished',
          'Completed' // Ultimo paso
        ];
        /** @var Model $modelName */
        $modelName = static::$modelNameStatic ?? config('sat.modelName');
        return $modelName::whereNotIn('status',$failed)->get()->transform(function ($item, $key){
            return new static(SatWSService::getInstance(), $item->request_id);
        });

    }

    public function __construct(SatWSService $service, $request_id)
    {
        $this->service = $service;
        $this->requestId = $request_id;
        return $this;
    }

    /**
     * @return $this
     * @throws SatWsRequestFailedException
     * @throws SatWsRequestRejectedException
     */
    public function verify(): SatWSRequestChecker
    {
        $status = $this->service->api()->verify($this->requestId);

        if (! $status->getStatus()->isAccepted()) {
            (new SatWSRequestStatusUpdater($this))->update();
            throw new SatWsRequestFailedException("Fallo al verificar la consulta {$this->requestId}: {$status->getStatus()->getMessage()}");
        }

        if (! $status->getCodeRequest()->isAccepted()) {
            (new SatWSRequestStatusUpdater($this))->update();
            throw new SatWsRequestRejectedException("La solicitud {$this->requestId} fue rechazada: {$status->getCodeRequest()->getMessage()}");
        }

        $this->statusRequest = $status->getStatusRequest();
        $this->status = $status;

        (new SatWSRequestStatusUpdater($this))->update();

        return $this;
    }

    public function isFinished()
    {
        return $this->statusRequest->isFinished();
    }

    public function getStatusName()
    {
        return $this->statusRequest->getName();
    }

    public function getRequestId()
    {
        return $this->requestId;
    }

    public function getService() : Service
    {
        return $this->service->api();
    }

    public function getPackagesIds(): array
    {
        return $this->status->getPackagesIds();
    }

}
