<?php


namespace App\Sat\Importer;


use App\Exceptions\SatWsRequestImportFailed;
use App\Sat\SatWSRequestChecker;
use App\Sat\SatWSRequestStatusUpdater;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;
use ReflectionClass;

abstract class ImporterType
{


    public function importAll(Builder $builder)
    {
        $className = ucfirst((new ReflectionClass($this))->getShortName());

        $all = $builder->$className()->get();

        Log::debug('Archivos a ser importados: ' . count($all));

        $all->each(function($item, $key){
            try {
                $this->import($item->file);
                SatWSRequestStatusUpdater::setImportStatusTrue($item);
            } catch (SatWsRequestImportFailed $e) {
                SatWSRequestStatusUpdater::setFailedImport($item);
                Log:debug($e->getMessage());
            }
        });
    }

    /**
     * @throws \Exception
     */
    public function import($file)
    {
        throw new SatWsRequestImportFailed("Función no implementada");
    }
}
