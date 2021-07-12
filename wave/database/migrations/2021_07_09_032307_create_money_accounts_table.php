<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMoneyAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('money_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // It's like Alias
            $table->string('type'); //Checks, Saving, Cash
            $table->string('bank')->nullable();
            $table->string('clabe')->nullable();
            $table->string('account_number')->nullable();
            $table->double('balance')->default(0);
            $table->string('currency')->default('MXN');
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
        Schema::dropIfExists('money_accounts');
    }
}
