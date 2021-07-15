<?php


namespace App\Sat\Importer;

use App\Models\Cfdi;
use App\Models\CfdiConcepto;
use App\Models\CfdiConceptoImpuesto;
use App\Models\CfdiImpuesto;
use App\Models\CfdiPago;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PhpCfdi\CfdiCleaner\Cleaner;
use PhpCfdi\CfdiToJson\JsonConverter;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;


class XMLUtil
{
    public function allFilesFrom($year = null, $month = null)
    {
        $year = $year ?? date('Y');
        $month = $month ?? date('m');

        collect(Storage::disk('sat')->allFiles("cfdis/$year/$month"))->each(function($item, $key){
            try {
                $this->importXmlFromContent(Storage::disk('sat')->get($item));
            } catch (\Illuminate\Contracts\Filesystem\FileNotFoundException $e) {
                Log::debug('Archivo no encontrato ' . $item);
            } catch (\JsonException $e) {
                Log::debug("El Archivo $item no es formato de json valido.");
            }
        });
    }

    /**
     * Vamos a validar el archivo XML o Zip que se ingreso, para ver como procedemos.
     */
    public function createCfdis()
    {

        $file = storage_path('app/public/') . $this->record['file'];
        Log::debug('Uploaded File: ' . $file);
        $extension = \File::extension($file);

        $success = 0;
        $failed = 0;
        switch ($extension) {
            case 'zip':
                $zip = new \ZipArchive();
                $zipStatus = $zip->open($file);

                if ($zipStatus === true) {
                    // Directorio Random
                    $tmpdir = Str::random(15);
                    $tmppath = storage_path('tmp') . '/' . $tmpdir;
                    // No necesitamos crear directorio, ya que el ZipExtract lo hace.
                    //if(!Storage::makeDirectory($tmpdir)) throw new \Exception('No se pudo crear el directorio temporal.');

                    Log::debug('ExtractTo:: ' . $tmppath);
                    Log::debug('> ls -al ' . $tmppath);
                    $zip->extractTo($tmppath);
                    $zip->close();
                    unlink($file);

                    Log::debug(exec('ls -al ' . $tmppath));
                    /*
                     * Import Files!
                     */
                    $xmlType = ["application/xml", "text/xml", "text/plain"];
                    $files = collect(preg_grep('/^([^.])/', scandir($tmppath)))->map(function ($file) use ($tmppath, $xmlType) {
                        $mimetype = File::mimeType($tmppath . '/' . $file);
                        $pathfile = $tmppath . '/' . $file;
                        Log::debug($mimetype . '::' . $pathfile);
                        if (in_array($mimetype, $xmlType)) {
                            $returnPath = Storage::putFile('public/xml', new File($pathfile));
                            Log::debug('StoragePath: ' . $returnPath);
                            return $returnPath;
                            //return new UploadedFile($pathfile, $file, $mimetype);
                        }
                    })->filter(); // Quitamos los files que inician con .


                    $files->each(function ($file) use (&$success, &$failed) {

                        $file = 'xml/' . Str::of($file)->afterLast('/');
                        Log::debug('$this->record["file"] = ' . $file);
                        $this->record['file'] = $file;

                        if ($this->importXmlFromFile(storage_path('app/public/') . $this->record['file'])) {
                            $success++;
                        } else {
                            $failed++;
                            unlink(storage_path('app/public/') . $this->record['file']);
                        }
                    });
                    \Storage::deleteDirectory('tmp/' . $tmpdir);
                }
                break;
            case 'xml':
                if ($this->singleImportXML($file)) {
                    $success++;
                } else {
                    $failed++;
                }
                break;
        }
        // Notificacion final de creación de XML
        $this->notify('CFDI Importados ' . $success . ' con ' . $failed . ' fallos.');
    }

    /**
     * Metodo que busca en el CFDI los conceptos de la factura para
     * registrarlos en la tabla cfdi_conceptos y así tener el control
     * en una tabla por separada de los conceptos.
     *
     * @param array $conceptos
     * @param array $id
     * @param $model
     * @param null $tipo
     */
    public function createConceptos(array $conceptos, array $id, $model, $tipo = null)
    {
        if (count($conceptos) < 1) return;

        foreach ($conceptos as $concepto) {
            if (count($concepto) > 0) {
                foreach ($concepto as $item) {
                    $c = $model::firstOrCreate(
                        array_merge(array_base($item), $id),
                        array_merge(array_base($item), $id)
                    );

                    if (isset($item['Impuestos'])) {
                        foreach ($item['Impuestos'] as $impuesto) {
                            foreach ($impuesto as $tipoDeImpuesto => $detalleImpuesto) {
                                $detalleImpuesto = array_merge(
                                    array_base($detalleImpuesto[0]),
                                    ['Tipo' => $tipoDeImpuesto],
                                    ['cfdi_concepto_id' => $c->id],
                                    $id,
                                );
                                $i = CfdiConceptoImpuesto::firstOrCreate($detalleImpuesto, $detalleImpuesto);
                            }
                        }
                    }
                }
            }
        }

    }

    /**
     * Metodo que busca en el CFDI los impuestos relacionados y los
     * registra en la tabla cfdi_impuestos.
     *
     * @param array $conceptos
     * @param array $id
     */
    public function createImpuestos(array $conceptos, array $id)
    {
        $impuestos = [];
        foreach ($conceptos as $k => $v) {
            if (!is_array($v)) continue;
            $impuestos[$k] = $v;
        }

        foreach ($impuestos as $impuesto) {
            foreach ($impuesto as $tipoImpuesto => $imp) {
                foreach ($imp as $k => $v) {
                    $impData = array_merge(
                        $id,
                        ['Tipo' => $tipoImpuesto],
                        $v
                    );
                    $i = CfdiImpuesto::firstOrCreate($impData, $impData);
                }
            }
        }
    }

    public function createPagos(array $pagos, array $id)
    {
        if (!array_key_exists($pagos['Pago'])) {
            throw new Exception('El complemento de Pago no tiene Pagos?');
        }

        $data = array_merge(
            array_base($pagos),
            array_base($pagos, 'Pago'),
            array_base($pagos['Pago'], 'DoctoRelacionado'),
            $id,
        );

        CfdiPago::firstOrCreate($data, $data);

    }

    /**
     * @param $content string
     * @return bool
     * @throws \JsonException
     */
    public function importXmlFromContent(string $content, $path = null): bool
    {
        $filepath = [ 'file' => $path ?? ''];
        $file = Cleaner::staticClean($content);

        // Intentamos cargar el archivo XML Correctaente.

        $json = json_decode(JsonConverter::convertToJson($file), true);


        /**
         * No tenemos soporte para CFDI v3.3
         */
        if (array_key_exists('version', $json)) {
            Log::critical('El CFDI suministrado no es version 3.3');
            throw new Exception('El CFDI suministrado no es version 3.3');
        }

        $cfdiData = array_merge($filepath,
            array_base($json),
            array_base($json, 'Emisor'),
            array_base($json, 'Receptor'),
            array_base($json, 'Impuestos', false),
            array_base($json['Complemento'][0], 'TimbreFiscalDigital'),
        );

        $cfdi = Cfdi::firstOrCreate(
            ['TimbreFiscalDigitalUUID' => $cfdiData['TimbreFiscalDigitalUUID']],
            $cfdiData
        );

        $this->createConceptos($json['Conceptos'], ['cfdi_id' => $cfdi->id], CfdiConcepto::class);

        if (array_key_exists('Impuestos', $json)) {
            $this->createImpuestos($json['Impuestos'], ['cfdi_id' => $cfdi->id]);
        }

        if ($json['TipoDeComprobante'] == 'P' && array_key_exists('Pagos', $json)) {
            $this->createPagos($json['Pagos'], ['cfdi_id' => $cfdi->id]);
        }

        return true;
    }

    /**
     * @param $xml string Path to XML File
     * @return bool
     * @throws Exception
     */
    public function importXmlFromFile(string $xml): bool
    {
        if(!file_exists($xml)) throw new FileNotFoundException('Archivo XML no encontrado.');

        $xmlType = ["application/xml", "text/xml", "text/plain"];
        $mimetype = \File::mimeType($xml);

        Log::debug($mimetype . '::' . $xml);

        if (!in_array($mimetype, $xmlType)) {
            throw new Exception('MIME Type invalido.');
        }
        try {
            $this->importXmlFromContent(file_get_contents($xml));
            return true;
        } catch (Exception $e)
        {
            Log::debug($e->getMessage());
            return false;
        }
    }


}
