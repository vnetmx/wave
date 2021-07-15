<?php


namespace App\Sat;


use App\Sat\Importer\ImporterType;
use Illuminate\Database\Eloquent\Model;

class SatWSRequestImporter
{
    /**
     * @var ImporterType
     */
    protected $type;
    /**
     * @var Model
     */
    protected $modelName;

    public static function import(ImporterType $import, $modelName=null)
    {
        $importer = new static();
        $importer->setType($import);
        if(isset($modelName)) $importer->setModelName($modelName);
        return $importer;
    }

    public function __construct()
    {
        $this->modelName = config('sat.modelName');
    }

    public function setType(ImporterType $type)
    {
        $this->type = $type;
        return $this;
    }

    public function setModelName($model)
    {
        $this->modelName = $model;
        return $this;
    }

    public function all()
    {
        $model = $this->modelName;
        // Revisamos en la BD todos los archivos que ya estan descargados y que no hemos importado
        $this->type->importAll($model::Downloaded()->NotImported());
    }


}
