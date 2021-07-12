<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePivotTableExpenseBankTransactionExapenseCategories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('expense_bank_transaction_expense_categories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('expense_category_id');
            $table->unsignedBigInteger('expense_bank_transaction_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('expense_bank_transaction_expense_categories');
    }
}
