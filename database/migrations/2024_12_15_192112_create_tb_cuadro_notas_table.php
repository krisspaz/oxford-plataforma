<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTbCuadroNotasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_cuadro_notas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('estudiante_id'); // Relación con la tabla de estudiantes
            $table->unsignedBigInteger('materia_id'); // Relación con la tabla de materias
            $table->string('bimestre'); // 'Bimestre 1', 'Bimestre 2', etc.
            $table->decimal('nota_final', 5, 2)->nullable(); // Nota final de la materia por bimestre
            $table->decimal('nota_acumulada', 5, 2)->nullable(); // Nota acumulada hasta el bimestre actual
            $table->string('ciclo_escolar');
            $table->string('cierre');
            $table->timestamps();

            // Claves foráneas
            $table->foreign('estudiante_id')->references('id')->on('estudiantes')->onDelete('cascade');
            $table->foreign('materia_id')->references('id')->on('materias')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tb_cuadro_notas');
    }
}
