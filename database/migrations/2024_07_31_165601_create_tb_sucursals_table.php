<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTbSucursalsTable extends Migration
{
    public function up()
    {
        Schema::create('tb_sucursals', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_sucursal');
            $table->unsignedBigInteger('municipio_id');
            $table->string('direccion');
            $table->unsignedBigInteger('estado_id');
            $table->timestamps();

            $table->foreign('municipio_id')->references('id')->on('tb_municipios')->onDelete('cascade');
            $table->foreign('estado_id')->references('id')->on('tb_estados')->onDelete('cascade');
        });

      
    }

    public function down()
    {
      
        Schema::dropIfExists('tb_sucursals');
    }
}
