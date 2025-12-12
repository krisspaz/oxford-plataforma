<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContenidoMateriasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contenido_materias', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->string('descripcion');
            $table->unsignedBigInteger('materia_id');
            $table->unsignedBigInteger('bimestre_id'); // Primer, Segundo, Tercer, Cuarto Bimestre
            $table->enum('tipo_contenido', ['video', 'audio', 'documento', 'imagen','link']);
            $table->string('archivo'); // Ruta o enlace del archivo
            $table->unsignedBigInteger('docente_id'); // Relación con el docente
            $table->timestamps();

            // Llaves foráneas
            $table->foreign('bimestre_id')->references('id')->on('bimestres')->onDelete('cascade');
            $table->foreign('materia_id')->references('id')->on('materias_cursos')->onDelete('cascade');
            $table->foreign('docente_id')->references('id')->on('docentes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contenido_materias');
    }
}
