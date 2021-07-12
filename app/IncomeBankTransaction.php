<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class IncomeBankTransaction extends Model
{
    use HasFactory;

    public function getTotalBrowseAttribute()
    {
        return "$" . $this->Total  . Str::upper($this->Moneda);
    }

    public function categories()
    {
        return $this->belongsToMany(TransactionCategory::class,'income_bank_transactions_transaction_categories','income_bank_transaction_id', 'transaction_category_id');
    }
}
