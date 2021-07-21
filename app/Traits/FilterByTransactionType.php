<?php


namespace App\Traits;


use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;

trait FilterByTransactionType
{
    public function isBrowsing()
    {
        return (Route::is('*.index') && request()->isMethod('get'));
    }

    public function isEditing()
    {
        return (Route::is('*.edit') && request()->isMethod('get'));
    }

    public function isSaving()
    {
        return (Route::is('*.store') && request()->isMethod('post'));
    }

    public function isDeleting()
    {
        return (Route::is('*.destroy') && request()->isMethod('delete'));
    }

    public function filterByType($query, $type = 'txType', $default = 'ingreso')
    {
        // Scoping Type Always must be set
        $type = "scope" . ucfirst(request()->input($type, $default));
        if(method_exists($this,$type)) {
            return $this->$type($query);
        }

        return $query;
    }

    public function scopeDateRange($query,Carbon $from,Carbon $to, $column = 'Fecha')
    {
        debug($from->toDateTimeString(), $to->toDateTimeString());

        return $query->whereBetween($column, [$from->toDateTimeString(), $to->toDateTimeString()]);

    }

    public function scopeFilterDate($query)
    {
        // Scoping Range Dates with Format d-m-Y
        $fromDate = request()->input('fromDate', null);
        $toDate = request()->input('toDate', null);
        if (isset($fromDate) && isset($toDate)) {
            try {
                $fromDate = Carbon::createFromFormat('Y-m-d', $fromDate)->minute(0)->hour(0)->second(0);
                $toDate = Carbon::createFromFormat('Y-m-d', $toDate)->hour(23)->minute(59)->second(59);

                if ($toDate->gt($fromDate)) {
                    return $this->scopeDateRange($query, $fromDate, $toDate);
                }
            } catch (InvalidFormatException $e) {
                // Bad Code!
            }
        }


        $year = request()->input('year', date('Y'));
        $month = request()->input('month', date('m'));
        return $this->scopeDateRange($query,
            Carbon::createFromDate($year, $month)->firstOfMonth()->minute(0)->hour(0)->second(0),
            Carbon::createFromDate($year, $month)->endOfMonth()->hour(23)->minute(59)->second(59)
        );
    }
}
