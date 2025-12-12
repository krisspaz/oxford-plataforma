<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFacturasEmitidasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('facturas_emitidas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pago_id'); // Referencia al ID de pagos
            $table->string('nit'); // Nit de la factura
            $table->string('guid'); // Guid de la factura
            $table->string('serie'); // Serie de la factura
            $table->string('numero'); // Número de la factura
            $table->string('link'); // Enlace al XML o PDF
            $table->string('anular');
            $table->string('motivo');
            

            // Relación con la tabla de pagos (si existe)
            $table->foreign('pago_id')->references('id')->on('pagos')->onDelete('restrict');
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
        Schema::dropIfExists('facturas_emitidas');
    }
}
