<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMateriasCursosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('materias_cursos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('docente_id');
            $table->unsignedBigInteger('materia_id');
            $table->foreignId('gshges_id')->constrained('pv_cgshges')->onDelete('cascade');
            $table->unsignedBigInteger('estado_id');
            $table->timestamps();


            // Claves foráneas

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
        Schema::dropIfExists('materias_cursos');
    }
}
