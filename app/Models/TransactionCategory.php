<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionCategory extends Model
{
    use HasFactory;
    public function transaction()
    {
        return $this->belongsToMany(BankTransaction::class,'bank_transactions_transaction_categories','transaction_category_id', 'bank_transaction_id');
    }
}
