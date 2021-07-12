<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIncomeBankTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('income_bank_transactions', function (Blueprint $table) {
            $table->id();
            // Fecha
            $table->dateTime('Fecha');
            // Forma de Pago
            $table->string('FormaPago');
            // Referencia
            $table->string('Referencia')->nullable();
            // Ordenante
            $table->string('Ordenante')->nullable();
            // Banco de Origen
            $table->string('BancoOrigen')->nullable();
            // Concepto
            $table->string('Concepto')->nullable();
            // Monto Total del Deposito
            $table->decimal('Total');
            // La Factura Relacionada
            $table->unsignedBigInteger('cfdi_id')->nullable();
            // Cliente
            $table->unsignedBigInteger('user_id')->nullable();
            // Columna para referencia si fue entre mismas cuentas.
            $table->unsignedBigInteger('entre_cuentas_id')->nullable();
            $table->foreign('entre_cuentas_id')->references('id')->on('money_accounts')->cascadeOnDelete();
            // Origen, Nacional o Extranjero
            $table->string('Origen')->default('Nacional');
            // Moneda de la transaccion
            $table->string('Moneda')->default('MXN');
            // Tipo de Cambio, es 1 para MXN
            $table->decimal('TipoCambio')->default(1);
            // Si borramos la cuenta al pito todas las transacciones..
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
        Schema::dropIfExists('income_bank_transactions');
    }
}
