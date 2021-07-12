<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePivotTableForIncomesAndCategoriesTransactions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('income_bank_transactions_transaction_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('income_bank_transaction_id');
            $table->foreignId('transaction_category_id');
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
        Schema::dropIfExists('pivot_table_for_incomes_and_categories_transactions');
    }
}
