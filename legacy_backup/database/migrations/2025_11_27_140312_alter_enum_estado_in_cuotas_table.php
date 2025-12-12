<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterEnumEstadoInCuotasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cuotas', function (Blueprint $table) {
            DB::statement("ALTER TABLE cuotas MODIFY estado ENUM('pendiente', 'pagada', 'vencida','baja voluntaria','baja por siniestro','baja por fuerza mayor','solicitud de baja') DEFAULT 'pendiente'");
        });
    }

    public function down()
    {
        Schema::table('cuotas', function (Blueprint $table) {
            DB::statement("ALTER TABLE cuotas MODIFY estado ENUM('pendiente', 'pagada', 'vencida') DEFAULT 'pendiente'");
        });
    }
}
