<?php

namespace App\Models;

use App\Traits\FilterByTransactionType;
use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class BankTransaction extends Model
{
    use HasFactory;
    use FilterByTransactionType;

    public $additional_attributes = ['details'];

    public function getTotalBrowseAttribute()
    {
        return "$" . number_format($this->Total) . Str::upper($this->Moneda);
    }

    public function getDetailsAttribute()
    {
        return "{$this->Concepto}";
    }

    public function categories()
    {
        return $this->belongsToMany(TransactionCategory::class,'bank_transactions_transaction_categories','bank_transaction_id', 'transaction_category_id');
    }

    /**
     * @deprecated
     * @param $query
     * @return mixed
     */
    public function scopeTransaccion($query)
    {
        if( ! $this->isBrowsing()) return $query;

        $ranged = false;
        // Scoping Type Always must be set
        $query = $this->filterByType($query);

        // Scoping Range Dates with Format d-m-Y
        $fromDate = request()->input('fromDate', null);
        $toDate = request()->input('toDate', null);
        if(isset($fromDate) && isset($toDate)) {
            try {
                $fromDate = Carbon::createFromFormat('d-m-Y',$fromDate);
                $toDate = Carbon::createFromFormat('d-m-Y', $toDate);

                if($toDate->gt($fromDate))
                {
                    $query = $this->scopeDateRange($query, $fromDate, $toDate);
                    $ranged = true;
                }
            } catch (InvalidFormatException $e)
            {
                // Bad Code!
            }
        }

        // Scoping default range words "lastmonth
        if(! $ranged && request()->input('range', false)) {
            $query = $this->filterByType($query, 'range', null);
            $ranged = true;
        }

        // Filter by Month
        $byMonth = request()->input('month', null);
        if(!$ranged && isset($byMonth))
        {
            $monthTranslate = [
                "enero" => "January",
                "febrero" => "February",
                "marzo" => "March",
                "abril" => "April",
                "mayo" => "May",
                "junio" => "June",
                "julio" => "July",
                "agosto" => "August",
                "septiembre" => "September",
                "octubre" => "October",
                "noviembre" => "November",
                "diciembre" => "December",
            ];

            if(array_key_exists($byMonth,$monthTranslate))
            {
                $query = $this->scopeDateRange($query,
                    Carbon::parse($monthTranslate[$byMonth])->firstOfMonth(),
                    Carbon::parse($monthTranslate[$byMonth])->endOfMonth()
                );
                $ranged = true;
            }
        }

        // Si no hay definido rango, entonces se filtra con el mes actual.
        if(!$ranged)
            $query = $this->scopeDateRange($query, Carbon::today()->firstOfMonth(), Carbon::today()->endOfMonth());

        return $query;
    }

    public function scopeIngreso($query)
    {
        return $query->where('TipoTransaccion', "ingreso");
    }

    public function scopeEgreso($query)
    {
        return $query->where('TipoTransaccion', "egreso");
    }

    public function scopeFilters($query)
    {
        if (!$this->isBrowsing()) return $query;

        // Scoping Type Always must be set 'txType'
        $query = $this->filterByType($query);
        // Scoping Dates
        return $this->scopeFilterDate($query);
    }
}
