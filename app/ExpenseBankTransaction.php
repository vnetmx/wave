<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ExpenseBankTransaction extends Model
{
    use HasFactory;

    public function getTotalBrowseAttribute()
    {
        return "$" . $this->Total  . Str::upper($this->Moneda);
    }

    public function categories()
    {
        return $this->belongsToMany(ExpenseCategory::class,'expense_bank_transaction_expense_categories','expense_bank_transaction_id', 'expense_category_id');

    }
}
