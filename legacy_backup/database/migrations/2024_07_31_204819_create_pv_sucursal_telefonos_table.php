<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePvSucursalTelefonosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pv_sucursal_telefonos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sucursal_id');
            $table->unsignedBigInteger('telefono_id');
            $table->timestamps();

            $table->foreign('sucursal_id')->references('id')->on('tb_sucursals')->onDelete('cascade');
            $table->foreign('telefono_id')->references('id')->on('tb_telefonos')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pv_sucursal_telefonos');
    }
}
