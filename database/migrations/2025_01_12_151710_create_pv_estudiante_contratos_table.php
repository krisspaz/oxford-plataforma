<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePvEstudianteContratosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pv_estudiante_contratos', function (Blueprint $table) {
            $table->id(); // ID principal
            $table->unsignedBigInteger('estudiante_id'); // Relación con estudiantes
            $table->unsignedBigInteger('contrato_id'); // Relación con contratos
            $table->string('contrato_firmado')->nullable(); // Si el contrato está firmado
            $table->enum('estado', ['Vigente', 'No Vigente'])->default('No Vigente'); // Estado del contrato
            $table->timestamps(); // Marca de tiempo para creado y actualizado

            // Claves foráneas
            $table->foreign('estudiante_id')->references('id')->on('estudiantes')->onDelete('cascade');
            $table->foreign('contrato_id')->references('id')->on('contratos')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pv_estudiante_contratos');
    }
}
