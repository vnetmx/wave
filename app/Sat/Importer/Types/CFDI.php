<?php


namespace App\Sat\Importer\Types;


use App\Exceptions\SatWsRequestImportFailed;
use App\Sat\Importer\ImporterType;
use App\Sat\Importer\XMLUtil;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use PhpCfdi\SatWsDescargaMasiva\PackageReader\CfdiPackageReader;
use PhpCfdi\SatWsDescargaMasiva\PackageReader\Exceptions\OpenZipFileException;

class CFDI extends ImporterType
{
    public function import($file)
    {
        throw_if(!file_exists($file), SatWsRequestImportFailed::class, "No se encontro el ZIP.");

        try {
            $cfdiReader = CfdiPackageReader::createFromFile($file);
        } catch(OpenZipFileException $e)
        {
            Log::debug($e->getMessage());
            throw new SatWsRequestImportFailed('Fallo la importación al procesar CfdiPackageReader en ' . $file);
        }
        /** @var Model $model */
        $model = config('sat.cfdiModel');
        Log::debug('XML Importados ' . $cfdiReader->count());

        $month = date('m');
        $year = date('Y');
        // leer todos los CFDI dentro del archivo ZIP con el UUID como llave
        foreach ($cfdiReader->cfdis() as $uuid => $content) {
            if(! Storage::disk('sat')->put("cfdis/$year/$month/$uuid.xml",$content))
            {
                Log::debug($content);
                throw new SatWsRequestImportFailed('Fallo la importación del XML con UUID ' . $uuid . ' en la ruta ' . "cfdis/$year/$month/$uuid.xml");
            }
            (new XMLUtil())->importXmlFromContent($content, storage_path('sat') . "/cfdis/$year/$month/$uuid.xml");

        }
    }
}
