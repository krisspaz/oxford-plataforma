<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEstudiantesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('estudiantes', function (Blueprint $table) {
            $table->id(); // Campo 'id' autoincremental
            $table->string('fotografia_estudiante')->nullable(); // Campo para la fotografía del estudiante, puede ser nulo
            $table->foreignId('persona_id')->constrained('personas')->onDelete('cascade'); // Clave foránea a 'personas'
            $table->string('carnet')->unique(); // Campo de texto único para el carnet
            $table->foreignId('cgshges_id')->constrained('pv_cgshges')->onDelete('cascade');
            $table->foreignId('estado_id')->constrained('tb_estados')->onDelete('cascade'); // Clave foránea a 'tb_estados'
            $table->timestamps(); // Campos 'created_at' y 'updated_at'
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('estudiantes');
    }
}
