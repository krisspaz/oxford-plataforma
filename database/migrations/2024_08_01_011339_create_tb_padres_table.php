<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTbPadresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_padres', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('apellido');
            $table->unsignedBigInteger('identificacion_documentos_id');
            $table->string('num_documento');
            $table->date('fecha_nacimiento');
            $table->string('profesion')->nullable();
            $table->string('telefono');
            $table->unsignedBigInteger('municipio_id');
            $table->text('direccion')->nullable();
            $table->timestamps();

            $table->foreign('identificacion_documentos_id')->references('id')->on('tb_identificacion_documentos')->onDelete('cascade');
            $table->foreign('municipio_id')->references('id')->on('tb_municipios')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tb_padres');
    }
}
