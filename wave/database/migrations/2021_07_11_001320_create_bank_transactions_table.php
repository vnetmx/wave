<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBankTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bank_transactions', function (Blueprint $table) {
            $table->id();
            // Cuentas Bancaria que se afecta con la transacción
            $table->foreignId('money_account_id')->constrained()->cascadeOnDelete();
            // Tipo (ingreso, egreso)
            $table->string('TipoTransaccion');
            // Fecha
            $table->dateTime('Fecha');
            // Forma de Pago
            $table->string('FormaPago');
            // Referencia
            $table->string('Referencia')->nullable();
            // Concepto
            $table->text('Concepto')->nullable();
            // Monto Total del Deposito
            $table->decimal('Total');
            // Cliente belongsTo
            $table->foreignId('user_id')->nullable();
            // Transaction belongsTo
            $table->foreignId('cfdi_id')->nullable();
            // Columna para referencia si fue entre mismas cuentas.
            $table->foreignId('entre_cuentas_id')->nullable()->constrained('money_accounts')->cascadeOnDelete();
            //$table->foreign('entre_cuentas_id')->references('id')->on('money_accounts')->cascadeOnDelete();
            // Origen, Nacional o Extranjero
            $table->string('Origen')->default('Nacional');
            // Moneda de la transaccion
            $table->string('Moneda')->default('MXN');
            // Tipo de Cambio, es 1 para MXN
            $table->decimal('TipoCambio')->default(1);
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
        Schema::dropIfExists('bank_transactions');
    }
}
