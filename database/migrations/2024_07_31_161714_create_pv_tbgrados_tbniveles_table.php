<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePvTbgradosTbnivelesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pv_tbgrados_tbniveles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('grado_id');
            $table->unsignedBigInteger('nivel_id');
            $table->unsignedBigInteger('estado_id');
            $table->timestamps();

            // Añadir las claves foráneas
            $table->foreign('grado_id')->references('id')->on('tb_grados')->onDelete('cascade');
            $table->foreign('nivel_id')->references('id')->on('tb_niveles')->onDelete('cascade');
            $table->foreign('estado_id')->references('id')->on('tb_estados')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pv_tbgrados_tbniveles');
    }
}
