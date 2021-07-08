<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('payment_gw')->nullable(); // el index de config/payments.php
            $table->string('authorization')->nullable(); // Es la autorización del gateway
            $table->string('order')->index(); // Este es el Order que en realidad solo será un ID que usaremos. (uniqid($user->id)) regresa 13 caracteres + prefix
            $table->string('currency')->default('MXN');
            $table->decimal('amount');
            $table->string('status');
            $table->string('transaction_type'); // 'charge' Algunos como Openpay ponen varios estados aquí.
            $table->string('transaction_id')->nullable();
            $table->ipAddress('client_ip');
            $table->string('user_agent');
            /**
             * And this transaction belongs to
             */
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->on('users')->references('id');
            /**
             * Vamos a usar lo mismo de Voyager, de usar los productos en string separados con ','
             * y un mutator para al accederlos crear un arreglo.
             */
            $table->string('item_name'); // 1,4,6,2
            $table->string('item_description')->nullable();
            $table->string('item_class')->nullable();

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
        Schema::dropIfExists('transactions');
    }
}
