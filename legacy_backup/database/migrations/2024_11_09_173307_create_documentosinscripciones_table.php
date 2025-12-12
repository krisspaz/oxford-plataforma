<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocumentosinscripcionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('documentosinscripciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('estudiante_id')->constrained('estudiantes')->onDelete('cascade');
            $table->string('tipo_documento'); // Tipo de documento (Ej. Identificación, Certificado)
            $table->string('nombre_documento'); // Nombre del documento
            $table->string('documento'); // Ruta del archivo del documento
            $table->date('fexpiracion'); // Fecha de expiración del documento
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
        Schema::dropIfExists('documentosinscripciones');
    }
}
