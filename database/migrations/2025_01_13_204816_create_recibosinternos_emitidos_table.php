<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRecibosinternosEmitidosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recibosinternos_emitidos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pago_id'); // Referencia al ID de pagos
            $table->string('nit'); // Nit
            $table->string('guid'); // Guid o certificado
            $table->string('serie'); // Serie de la factura
            $table->string('numero'); // Número de la factura
            $table->string('link'); // Enlace al XML o PDF
            

            // Relación con la tabla de pagos (si existe)
            $table->foreign('pago_id')->references('id')->on('pagos')->onDelete('cascade');
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
        Schema::dropIfExists('recibosinternos_emitidos');
    }
}
