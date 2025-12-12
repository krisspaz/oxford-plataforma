<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTbMadresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_madres', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('apellido');
            $table->unsignedBigInteger('identificacion_documentos_id');
            $table->string('num_documento');
            $table->date('fecha_nacimiento');
            $table->string('profesion');
            $table->string('telefono');
            $table->unsignedBigInteger('municipio_id');
            $table->text('direccion');
            $table->timestamps();

            $table->foreign('identificacion_documentos_id')->references('id')->on('tb_identificacion_documentos');
            $table->foreign('municipio_id')->references('id')->on('tb_municipios');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tb_madres');
    }
}
