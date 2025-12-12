<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdministrativosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('administrativos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('persona_id')->constrained('personas')->onDelete('restrict'); // Clave foránea a 'personas'
            $table->foreignId('estado_id')->constrained('tb_estados')->onDelete('restrict'); // Clave foránea a 'tb_estados'

            $table->string('fotografia_docente')->nullable();

            $table->foreignId('cargo_id')->constrained('cargos')->onDelete('restrict'); // Clave foránea a 'tb_estados'
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
        Schema::dropIfExists('administrativos');
    }
}
