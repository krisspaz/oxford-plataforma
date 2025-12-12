<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFamiliasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('familias', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_familiar'); // Nombre de la familia o grupo familiar
            $table->string('codigo_familiar')->key(); // Código único que identifica a la familia
            $table->foreignId('padre_persona_id')->nullable()->constrained('personas')->onDelete('set null'); // Persona relacionada con el padre
            $table->foreignId('madre_persona_id')->nullable()->constrained('personas')->onDelete('set null'); // Persona relacionada con la madre
            $table->foreignId('encargado_persona_id')->nullable()->constrained('personas')->onDelete('set null'); // Persona relacionada con el encargado
            $table->foreignId('estudiante_id')->nullable()->constrained('estudiantes')->onDelete('set null'); // Persona relacionada con el estudiante
            $table->foreignId('estado_id')->constrained('tb_estados')->onDelete('cascade'); // Estado de la familia
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
        Schema::dropIfExists('familias');
    }
}
