<?php


namespace App\Sat;


use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class SatWSRequestDownloader
{
    protected $packagesIds = [];
    protected $checker;

    /**
     * SatWSRequestDownloader constructor.
     * @param SatWSRequestChecker $checker
     */
    public function __construct(SatWSRequestChecker $checker)
    {
        $this->packagesIds = $checker->getPackagesIds();
        $this->checker = $checker;
        return $this;
    }

    public function count(): int
    {
        return count($this->packagesIds);
    }

    public function download(): Collection
    {
        $files = collect($this->packagesIds)->transform(function($item,$key) {
           $downloadRequest = $this->checker->getService()->download($item);
           if($downloadRequest->getStatus()->isAccepted())
           {
               if(!Storage::disk('sat')->exists("webservice/$item.zip"))
               {
                   if (Storage::disk('sat')->put("webservice/$item.zip", $downloadRequest->getPackageContent()))
                   {

                       return storage_path('sat') . "/webservice/$item.zip";
                   }
               }
           }
           return null;
        })->filter();

        if($files->count()>0)
        {
            (new SatWSRequestStatusUpdater($this->checker))
                ->filePath(implode(',',$files->toArray()))
                ->update('Downloaded');
        }

        return $files;
    }

}
