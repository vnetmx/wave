<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCfdiConceptosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cfdi_conceptos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cfdi_id')->constrained();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('Tipo')->index()->default('Concepto'); // "Concepto", "Parte"
            $table->string('ClaveProdServ');
            $table->string('NoIdentificacion')->nullable();
            $table->string('Cantidad');
            $table->string('ClaveUnidad');
            $table->string('Unidad')->nullable();
            $table->string('Descripcion');
            $table->string('ValorUnitario');
            $table->string('Importe');
            $table->string('Descuento')->nullable();

            /**
             * NODO InformacionAduanera
             */
            $table->string('InformacionAduaneraNumeroPedimento')->nullable();
            /**
             * NODO CuentaPredial
             */
            $table->string('CuentaPredialNumero')->nullable();

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
        Schema::table('cfdi_conceptos', function (Blueprint $table) {
            $table->dropForeign(['cfdi_id']);
        });
        Schema::dropIfExists('cfdi_conceptos');
    }
}
