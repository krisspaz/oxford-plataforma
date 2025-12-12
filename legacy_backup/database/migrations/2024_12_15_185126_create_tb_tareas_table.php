<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTbTareasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_tareas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bimestre_id');
            $table->string('titulo');
            $table->string('descripcion');
            $table->string('punteo');
            $table->unsignedBigInteger('materia_id');
            $table->unsignedBigInteger('docente_id');
            $table->date('fexpiracion');
            $table->boolean('tiempo_extra_automatico');
            $table->unsignedBigInteger('estado_id');
            $table->timestamps();

            // Claves foráneas
            $table->foreign('bimestre_id')->references('id')->on('bimestres');
            $table->foreign('docente_id')->references('id')->on('docentes');
            $table->foreign('materia_id')->references('id')->on('materias');
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
        Schema::dropIfExists('tb_tareas');
    }
}
