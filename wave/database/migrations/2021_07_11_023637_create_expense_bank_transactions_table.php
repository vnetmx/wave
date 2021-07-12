<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExpenseBankTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('expense_bank_transactions', function (Blueprint $table) {
            $table->id();
            // Fecha
            $table->dateTime('Fecha');
            // Forma de Pago
            $table->string('FormaPago');
            // Referencia
            $table->string('Referencia')->nullable();
            // Concepto
            $table->string('Concepto')->nullable();
            // Monto Total del Deposito
            $table->decimal('Total');
            // La Factura Relacionada
            $table->unsignedBigInteger('cfdi_id')->nullable();
            // Origen, Nacional o Extranjero
            $table->string('Origen')->default('Nacional');
            // Moneda de la transaccion
            $table->string('Moneda')->default('MXN');
            // Tipo de Cambio, es 1 para MXN
            $table->decimal('TipoCambio')->default(1);
            // Cuenta de Banco de donde se fue al camois
            $table->foreignId('money_account_id')->constrained()->cascadeOnDelete();
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
        Schema::dropIfExists('expense_bank_transactions');
    }
}
