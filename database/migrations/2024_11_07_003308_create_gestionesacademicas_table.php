<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGestionesacademicasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gestionesacademicas', function (Blueprint $table) {
            $table->id();
            $table->string('gestion');
            $table->string('ciclo_escolar');
            $table->string('resolucion_DIACO')->nullable();
            $table->string('resolucion_Ministerial')->nullable();
            $table->foreignId('sucursal_id')->nullable()->constrained('tb_sucursals')->onDelete('cascade');
            $table->foreignId('estado_id')->constrained('tb_estados')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('gestionesacademicas');
    }
}
