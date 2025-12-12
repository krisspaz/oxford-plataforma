<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProductosSeleccionadosIdToCuotasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cuotas', function (Blueprint $table) {
            $table->unsignedBigInteger('productos_seleccionados_id')->nullable()->after('convenio_id');

            $table->foreign('productos_seleccionados_id', 'pkproductos_seleccionados_id_foreign')
                  ->references('id')
                  ->on('productos_seleccionados')
                  ->onDelete('restrict')
                  ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cuotas', function (Blueprint $table) {
            $table->dropForeign('pkproductos_seleccionados_id_foreign');
            $table->dropColumn('productos_seleccionados_id');
        });
    }
}
