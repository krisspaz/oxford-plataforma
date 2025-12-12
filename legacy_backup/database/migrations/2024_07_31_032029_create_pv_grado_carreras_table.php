<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePvGradoCarrerasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pv_grado_carreras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grado_id')->constrained('tb_grados')->onDelete('cascade');
            $table->foreignId('carrera_id')->constrained('tb_carreras')->onDelete('cascade');
            $table->foreignId('estado_id')->constrained('tb_estados')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pv_grado_carreras');
    }
}
