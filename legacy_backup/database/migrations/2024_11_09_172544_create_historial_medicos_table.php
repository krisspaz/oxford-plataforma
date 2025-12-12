<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHistorialMedicosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('historial_medicos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('estudiante_id')->constrained('estudiantes')->onDelete('cascade');
            $table->string('grupo_sanguineo')->nullable();
            $table->text('alergias')->nullable();
            $table->text('enfermedades')->nullable();
            $table->text('medicamentos')->nullable();
            $table->string('medico')->nullable();
            $table->string('telefono_medico')->nullable();
            $table->text('observacion')->nullable();
            $table->foreignId('estado_id')->constrained('tb_estados')->onDelete('cascade');
            $table->timestamps(); // created_at y updated_at
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('historial_medicos');
    }
}
