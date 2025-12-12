<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTbCalificacionesTareasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_calificaciones_tareas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tarea_estudiante_id'); // Relación con la tabla tb_tarea_estudiantes
            $table->decimal('calificacion', 5, 2)->nullable(); // Para almacenar la calificación numérica
            $table->text('comentarios')->nullable(); // Comentarios adicionales del docente sobre la tarea
            $table->timestamps();

            // Clave foránea
            $table->foreign('tarea_estudiante_id')->references('id')->on('tb_tarea_estudiantes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tb_calificaciones_tareas');
    }
}
