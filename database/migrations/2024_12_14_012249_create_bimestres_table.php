<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBimestresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bimestres', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ciclo_escolar');
          
            $table->string('nombre')->unique(); // Nombre del bimestre (Ej: Bimestre 1, Bimestre 2)
            $table->date('fecha_inicio'); // Fecha de inicio del bimestre
            $table->date('fecha_culminacion'); // Fecha de culminación del bimestre
            $table->double('punteo_maximo', 8, 2);
            $table->timestamps();

            $table->foreign('ciclo_escolar')->references('id')->on('gestionesacademicas');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bimestres');
    }
}
