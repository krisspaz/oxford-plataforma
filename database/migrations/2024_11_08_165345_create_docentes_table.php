<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocentesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('docentes', function (Blueprint $table) {
            $table->id(); // Campo 'id' autoincremental
            $table->foreignId('persona_id')->constrained('personas')->onDelete('cascade'); // Clave foránea a 'personas'
            $table->string('fotografia_docente')->nullable();
            $table->string('especialidad'); // Campo de texto para la especialidad
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
        Schema::dropIfExists('docentes');
    }
}
