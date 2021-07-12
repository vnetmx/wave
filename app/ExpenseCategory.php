<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseCategory extends Model
{
    use HasFactory;

    public function transactions()
    {
        return $this->belongsToMany(ExpenseCategory::class,'expense_bank_transaction_expense_categories','expense_category_id', 'expense_bank_transaction_id');

    }
}
