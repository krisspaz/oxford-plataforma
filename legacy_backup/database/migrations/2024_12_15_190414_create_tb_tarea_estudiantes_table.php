<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTbTareaEstudiantesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_tarea_estudiantes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tarea_id'); // Relación con la tabla de tareas
            $table->unsignedBigInteger('estudiante_id'); // Relación con la tabla de estudiantes
            $table->string('archivo')->nullable(); // Para almacenar el archivo que sube el estudiante
            $table->date('fecha_entrega')->nullable(); // Para registrar la fecha de entrega de la tarea
            $table->enum('estado', ['pendiente', 'entregada', 'calificada'])->default('pendiente'); // Estado de la tarea
            $table->timestamps();
    
            // Claves foráneas
            $table->foreign('tarea_id')->references('id')->on('tb_tareas')->onDelete('cascade');
            $table->foreign('estudiante_id')->references('id')->on('estudiantes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tb_tarea_estudiantes');
    }
}
