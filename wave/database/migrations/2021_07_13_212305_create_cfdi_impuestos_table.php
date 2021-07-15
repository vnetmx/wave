<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCfdiImpuestosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cfdi_impuestos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cfdi_id')->constrained();
            $table->string('Tipo'); // "Traslado", "Retencion".

            $table->string('Impuesto'); // Existe en Traslado y Retencion
            $table->string('TipoFactor')->nullable(); // Solo Existe en Traslado
            $table->double('TasaOCuota')->nullable();// Solo Existe en Traslado
            $table->double('Importe'); // Existe en Traslado y Retencion

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
        Schema::table('cfdi_impuestos', function (Blueprint $table) {
            $table->dropForeign(['cfdi_id']);
        });
        Schema::dropIfExists('cfdi_impuestos');
    }
}
