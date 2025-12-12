<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFacturasEmitidasBitacorasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('facturas_emitidas_bitacoras', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pago_id');
            $table->string('nit');
            $table->string('guid');
            $table->string('serie');
            $table->string('numero');
            $table->text('link');
            $table->string('anular');
            $table->string('motivo');
            $table->timestamp('anulada_en'); // Nuevo campo para saber cuándo fue eliminada
            $table->timestamps();
            $table->unsignedInteger('cms_users_id')->nullable();
            $table->foreign('cms_users_id')->references('id')->on('cms_users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('facturas_emitidas_bitacoras');
    }
}
