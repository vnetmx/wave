<?php

namespace App\Sat;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class SatWSRequestStatusUpdater
{
    protected $checker;
    /**
     * @var Model
     */
    protected $model;
    protected static $modelNameStatic;

    public static function setModelName($modelName)
    {
        static::$modelNameStatic = $modelName;
    }

    public static function create($data)
    {
        $modelName = static::$modelNameStatic ?? config('sat.modelName');
        $modelName::create($data);
    }

    public static function setImportStatusTrue($item) : bool
    {
        $item->status = "Completed";
        $item->imported = 1;
        return $item->save();
    }

    public static function setFailedImport($item) : bool
    {
        $item->status = "ImportFailed";
        $item->message = "La importación del archivo " . $item->file . " falló!";
        return $item->save();
    }

    public static function setDownloaded($item) : bool
    {
        $item->status = "Downloaded";
        $item->imported = 0;
        $item->message = "";
        return $item->save();
    }

    public static function resetFailedImport()
    {
        /** @var Model $modelName */
        $modelName = static::$modelNameStatic ?? config('sat.modelName');
        $modelName::whereStatus('ImportFailed')->get()->each(function($item, $key) {
           $item->status = "Downloaded";
           $item->imported = 0;
           $item->save();
        });
    }

    public function __construct(SatWSRequestChecker $checker, $modelName = '')
    {
        $this->checker = $checker;
        $modelName = !empty($modelName) ? $modelName : config('sat.modelName');
        /** @var Model $modelName */
        $this->model = $modelName::whereRequestId($this->checker->getRequestId())->firstOrFail();

        return $this;
    }

    public function update($status = null)
    {
        $this->model->status = $status ?? $this->checker->getStatusName();
        return $this->save();
    }

    public function filePath($filePath): SatWSRequestStatusUpdater
    {
        $this->model->file = $filePath;
        return $this;
    }

    protected function save()
    {
        return $this->model->save();
    }
}
