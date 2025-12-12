<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTbAlumnosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_alumnos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->unique();
            $table->string('carne')->unique();
            $table->string('nombre');
            $table->string('apellidos');
            $table->enum('genero', ['M', 'F']);
            $table->string('cui')->unique();
            $table->date('fecha_nacimiento');
            $table->unsignedBigInteger('municipio_id');
            $table->string('direccion');
            $table->string('telefono')->nullable();
            $table->unsignedBigInteger('estado_id');
            $table->timestamps();
            $table->foreign('municipio_id')->references('id')->on('tb_municipios');
            $table->foreign('estado_id')->references('id')->on('tb_estados');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tb_alumnos');
    }
}
