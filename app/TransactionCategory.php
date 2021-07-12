<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionCategory extends Model
{
    use HasFactory;
    public function income_transaction()
    {
        return $this->belongsToMany(IncomeBankTransaction::class,'income_bank_transactions_transaction_categories','transaction_category_id', 'income_bank_transaction_id');
    }
}
