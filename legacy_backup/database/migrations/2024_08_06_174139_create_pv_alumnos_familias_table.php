<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePvAlumnosFamiliasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pv_alumnos_familias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alumno_id')->constrained('tb_alumnos')->onDelete('cascade');
            $table->foreignId('padres_tutores_id')->constrained('pv_padres_tutores')->onDelete('cascade');
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
        Schema::dropIfExists('pv_alumnos_familias');
    }
}
