<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePvNivelesSucursalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pv_niveles_sucursals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sucursal_id');
            $table->unsignedBigInteger('nivel_id');
            $table->unsignedBigInteger('estado_id');
            $table->timestamps();

            $table->foreign('sucursal_id')->references('id')->on('tb_sucursals')->onDelete('cascade');
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
        Schema::dropIfExists('pv_niveles_sucursals');
    }
}
