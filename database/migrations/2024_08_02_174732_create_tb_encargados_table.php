<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTbEncargadosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_encargados', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parentesco_id');
            $table->string('nombre');
            $table->string('apellido');
            $table->unsignedBigInteger('identificacion_documentos_id');
            $table->string('num_documento');
            $table->date('fecha_nacimiento');
            $table->string('profesion')->nullable();
            $table->string('telefono');
            $table->unsignedBigInteger('municipio_id');
            $table->string('direccion');
            $table->timestamps();

            $table->foreign('parentesco_id')->references('id')->on('tb_parentescos')->onDelete('cascade');
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
        Schema::dropIfExists('tb_encargados');
    }
}
