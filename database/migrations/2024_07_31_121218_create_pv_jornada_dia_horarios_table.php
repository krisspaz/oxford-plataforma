<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePvJornadaDiaHorariosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pv_jornada_dia_horarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jornada_id')->constrained('tb_jornadas')->onDelete('cascade');
            $table->foreignId('dia_id')->constrained('tb_dias')->onDelete('cascade');
            $table->foreignId('horario_id')->constrained('tb_horarios')->onDelete('cascade');
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
        Schema::dropIfExists('pv_jornada_dia_horarios');
    }
}
