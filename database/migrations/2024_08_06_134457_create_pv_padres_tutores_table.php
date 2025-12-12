<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePvPadresTutoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pv_padres_tutores', function (Blueprint $table) {
            $table->id();
            $table->string('codigofamiliar')->unique();
            $table->unsignedBigInteger('padre_id')->nullable();
            $table->unsignedBigInteger('madre_id')->nullable();
            $table->unsignedBigInteger('encargado_id')->nullable();
            $table->timestamps();

            $table->foreign('padre_id')->references('id')->on('tb_padres')->onDelete('cascade');
            $table->foreign('madre_id')->references('id')->on('tb_madres')->onDelete('cascade');
            $table->foreign('encargado_id')->references('id')->on('tb_encargados')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pv_padres_tutores');
    }
}
