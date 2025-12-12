<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMaxDigitosToTbIdentificacionDocumentosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tb_identificacion_documentos', function (Blueprint $table) {
            $table->integer('max_digitos')->nullable()->after('descripcion'); // tu nuevo campo
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tb_identificacion_documentos', function (Blueprint $table) {
            $table->dropColumn('max_digitos');
        });
    }
}
