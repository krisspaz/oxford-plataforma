<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTbDireccionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_direccion', function (Blueprint $table) {
            $table->id();
            // Relacion con 'tb_municipios'
            $table->foreignId('pais_id')->nullable()->constrained('tb_paises')->onDelete('set null');
            $table->foreignId('municipio_id')->constrained('tb_municipios');
            $table->string('direccion');
            $table->string('telefono_casa')->nullable();
            $table->string('telefono_mobil');
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
        Schema::dropIfExists('tb_direccion');
    }
}
