<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMateriasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('materias', function (Blueprint $table) {
            $table->id();
            
            $table->unsignedBigInteger('materia_id');
            $table->unsignedBigInteger('cgshe_id');
            $table->unsignedBigInteger('estado_id');
            $table->timestamps();
            $table->foreign('materia_id')->references('id')->on('gestion_materias');
            $table->foreign('cgshe_id')->references('id')->on('pv_cgshges');
            $table->foreign('estado_id')->references('id')->on('tb_estados');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('materias');
    }
}
