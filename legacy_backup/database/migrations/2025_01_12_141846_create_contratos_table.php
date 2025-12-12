<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContratosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contratos', function (Blueprint $table) {
            $table->id();
            $table->string('numero_contrato')->unique(); // Número único del contrato
            $table->unsignedBigInteger('inscripcion_id'); // Relación con la tabla de usuarios
            $table->unsignedBigInteger('persona_id'); // Relación con la tabla de usuarios
            $table->date('fecha_inicio'); // Fecha de inicio del contrato
            $table->date('fecha_fin')->nullable(); // Fecha de fin del contrato, opcional
            $table->string('estado')->default('activo'); // Estado del contrato (activo, finalizado, etc.)
            $table->string('archivo')->nullable(); // Ruta al archivo asociado
            $table->text('descripcion')->nullable(); // Descripción adicional
            $table->string('ciclo_escolar')->nullable(); // Ruta al archivo asociado
            $table->timestamps();

            // Relación de clave foránea con la tabla de usuarios
            $table->foreign('persona_id')->references('id')->on('personas')->onDelete('cascade');
            $table->foreign('inscripcion_id')->references('id')->on('inscripciones')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contratos');
    }
}
