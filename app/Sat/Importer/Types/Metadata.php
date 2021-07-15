<?php


namespace App\Sat\Importer\Types;


use App\Sat\Importer\ImporterType;
use Illuminate\Database\Eloquent\Model;
use PhpCfdi\SatWsDescargaMasiva\PackageReader\Exceptions\OpenZipFileException;
use PhpCfdi\SatWsDescargaMasiva\PackageReader\MetadataPackageReader;

class Metadata extends ImporterType
{

    /**
     * @param $file
     * @throws OpenZipFileException
     */
    public function import($file)
    {

        $metadataReader = MetadataPackageReader::createFromFile($file);

        /** @var Model $model */
        $model = config('sat.cfdiModel');

        // leer todos los registros de metadata dentro de todos los archivos del archivo ZIP
        foreach ($metadataReader->metadata() as $uuid => $metadata) {
            echo $metadata->uuid, ': ', $metadata->fechaEmision, PHP_EOL;
        }
    }
}
