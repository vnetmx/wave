<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCfdiConceptoImpuestosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cfdi_concepto_impuestos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cfdi_concepto_id')->constrained();
            $table->foreignId('cfdi_id')->constrained();
            $table->string('Tipo')->index()->default('Traslado'); // "Traslado" o "Retencion"
            /**
            Cuando un concepto no registra la información de algún impuesto,
            implica que no es objeto del mismo, excepto en los casos en que la
            LIEPS establece que en el comprobante fiscal no se deberá trasladar
            en forma expresa y por separado el impuesto.
            Si se registra información en este nodo, debe existir al menos una de
            las dos secciones siguientes: Traslados o Retenciones.
             */

            $table->double('Base');
            $table->string('Impuesto');
            $table->string('TipoFactor');
            $table->double('TasaOCuota');
            /**
            Se puede registrar el importe del impuesto trasladado que aplica a
            cada concepto. No se permiten valores negativos. Este campo es
            requerido cuando en el campo TipoFactor se haya registrado como
            Tasa o Cuota.
             */
            $table->double('Importe');

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
        Schema::table('cfdi_concepto_impuestos', function (Blueprint $table) {
            $table->dropForeign(['cfdi_concepto_id']);
            $table->dropForeign(['cfdi_id']);
        });
        Schema::dropIfExists('cfdi_concepto_impuestos');
    }
}
