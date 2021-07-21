<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCfdisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cfdis', function (Blueprint $table) {
            $table->id();
            /**
             * Campos personalizados para uso del sistema, no pertenecen al CFDI como tal
             */
            $table->string('file')->nullable();
            $table->foreignId('bank_transaction_id')->nullable()->constrained('bank_transactions')->nullOnDelete();
            $table->dateTime('FechaCancelacion')->nullable(); // Fecha de Cancelación en caso de Tenerla
            $table->dateTime('FechaPago')->nullable();
            /**
             * NODO COMPROBANTE
             */
            $table->double('Version');
            $table->string('Serie')->nullable();
            $table->string('Folio')->nullable();
            $table->dateTime('Fecha');
            $table->text('Sello');
            /**
             * En el caso de que no se reciba el pago de la contraprestación
             * al momento de la emisión del comprobante fiscal (pago en
             * parcialidades o diferido), los contribuyentes deberán
             * seleccionar la clave 99 (Por definir) del catálogo
             * c_FormaPago publicado en el Portal del SAT.
             *
             * c_FormaPago Descripción
             * 01 Efectivo
             * 02 Cheque nominativo
             * 03 Transferencia electrónico de fondos
             * 99 Por defin
             */
            $table->string('FormaPago')->nullable();
            $table->string('NoCertificado');
            $table->text('Certificado');
            /**
             * Si el Tipo de Comprobante es I o E, entonces:
             * Se pueden registrar las condiciones comerciales aplicables para el
             * pago del comprobante fiscal, cuando existan éstas.
             *
             * En este campo se podrán registrar de 1 hasta 1000 caracteres.
             *
             * Ejemplo:
             * CondicionesDePago= 3 meses
             */
            $table->string('CondicionesDePago')->nullable();
            /**
             * Es la suma de los importes de los conceptos antes de descuentos e impuestos.
             *
             * No se permiten valores negativos.
             *
             * Este campo debe tener hasta la cantidad de decimales que soporte la moneda, ver ejemplo del campo Moneda.
             * Cuando en el campo TipoDeComprobante sea I (Ingreso), E (Egreso) o N (Nómina), el importe registrado en
             * este campo debe ser igual al redondeo de la suma de los importes de los conceptos registrados.
             * Cuando en el campo TipoDeComprobante sea T (Traslado) o P (Pago) el importe registrado en este campo debe
             * ser igual a cero.
             */
            $table->double('SubTotal');
            /**
             * Se puede registrar el importe total de los descuentos aplicables
            antes de impuestos. No se permiten valores negativos. Se debe
            registrar cuando existan conceptos con descuento.
             Este campo debe tener hasta la cantidad de decimales que
            soporte la moneda, ver ejemplo del campo Moneda.
             El valor registrado en este campo debe ser menor o igual que
            el campo Subtotal.
             Cuando en el campo TipoDeComprobante sea I (Ingreso),
            E (Egreso) o N (Nómina), y algún concepto incluya un
            descuento, este campo debe existir y debe ser igual al
            redondeo de la suma de los campos Descuento registrados
             */
            $table->double('Descuento')->nullable();
            $table->string('Moneda')->default('MXN');
            $table->double('TipoCambio')->default(1);
            /**
             * Es la suma del subtotal, menos los descuentos aplicables, más las
            contribuciones recibidas (impuestos trasladados - federales o
            locales, derechos, productos, aprovechamientos, aportaciones de
            seguridad social, contribuciones de mejoras) menos los impuestos
            retenidos federales o locales.
             *
             * Cuando el campo TipoDeComprobante sea T (Traslado) o
             * P (Pago), el importe registrado en este campo debe ser
             * igual a cero.
             */
            $table->double('Total');
            /**
             * Reglas
             *
             * 1. No debe existir el campo CondicionesDePago cuando sea T,P o N.
             * 2. No debe existir el campo de Descuento cuando T o P.
             * 3. No debe existir el campo Impuestos cuando T, P o N
             * 4. No debe exisitr MetodoPago y FormaPago cuando T o P.
             */
            $table->string('TipoDeComprobante'); // "T" Traslado, "P" Pago, "N" Nomina, "I" Ingreso, "E" Egreso.
            $table->string('MetodoPago')->nullable(); // "PUE", "PPD". Cuando FormaPago sea 99 debe ser PPD
            $table->string('LugarExpedicion');
            /**
             * Se debe registrar la clave de confirmación única e irrepetible que
             * entrega el proveedor de certificación de CFDI o el SAT a los
             * emisores (usuarios) para expedir el comprobante con importes o
             * tipo de cambio fuera del rango establecido o en ambos casos.
             */
            $table->string('Confirmacion')->nullable();
            /**
             * NODO CfdiRelacionados
             * Vamos a usar prefijo para este nodo.
             * CfdiRelacionados
             */
            $table->string('CfdiRelacionadosTipoRelacion')->nullable();
            /**
             * c_TipoRelacion Descripción
             * 01 Nota de crédito de los documentos relacionados
             * 02 Nota de débito de los documentos relacionados
             * 03 Devolución de mercancía sobre facturas o traslados previos
             * 04 Sustitución de los CFDI previos
             * 05 Traslados de mercancías facturados previamente
             * 06 Factura generada por los traslados previos
             * 07 CFDI por aplicación de anticipo
             */
            $table->string('CfdiRelacionadosUUID')->nullable();
            /**
             * NODO Emisor
             */
            $table->string('EmisorRfc');
            $table->string('EmisorNombre')->nullable();
            $table->string('EmisorRegimenFiscal'); // 601,
            /**
             * NODO Receptor
             */
            $table->string('ReceptorRfc');
            $table->string('ReceptorNombre')->nullable();
            /**
             * Ejemplo: Si la residencia fiscal de la empresa extranjera receptora
             * del comprobante fiscal se encuentra en Estados Unidos de América,
             * se debe registrar lo siguiente:
             * ResidenciaFiscal= USA
             * c_Pais Descripción
             * USA Estados Unidos
             *
             * Este campo es obligatorio cuando el RFC del receptor es un RFC
             * genérico extranjero, y se incluya el complemento de comercio
             * exterior o se registre el campo NumRegIdTrib.
             */
            $table->string('ReceptorResidenciaFiscal')->nullable();
            $table->string('ReceptorNumRegIdTrib')->nullable();
            $table->string('ReceptorUsoCFDI');

            /**
             * NODO Conceptos
             *
             * Los nodos conceptos los agarraremos de otro table
             */
            /**
             * NODO IMPUESTOS
             *
             * En caso de que el TipoDeComprobante sea T (Traslado),
             * (Nómina), o P (Pago), este elemento no debe existir.
             */
            $table->double('TotalImpuestosRetenidos')->nullable();
            $table->double('TotalImpuestosTrasladados')->nullable();

            /**
             * NODO TimbreFiscalDigital
             * Este nodo se encuentra del nodo Complemento[0], no se si haya más de 1 o asi.
             */
            $table->string("TimbreFiscalDigitalVersion");
            $table->string("TimbreFiscalDigitalUUID")->index();
            $table->string("TimbreFiscalDigitalFechaTimbrado");
            $table->string("TimbreFiscalDigitalRfcProvCertif");
            $table->text("TimbreFiscalDigitalSelloCFD");
            $table->string("TimbreFiscalDigitalNoCertificadoSAT");
            $table->text("TimbreFiscalDigitalSelloSAT");

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
        Schema::dropIfExists('cfdis');
    }
}
