<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRecibosEmitidosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recibos_emitidos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pago_id'); // Referencia al ID de pagos
        
            $table->string('serie'); // Serie de la recibo
            $table->string('numero'); // Número de la recibo
            $table->string('link'); // Enlace al XML o PDF

            $table->string('anular');
            $table->string('motivo');
            

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
        Schema::dropIfExists('recibos_emitidos');
    }
}
