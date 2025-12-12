<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBajaToCuotasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cuotas', function (Blueprint $table) {
            $table->string('baja')->nullable()->after('estado');
        });
    }

    public function down()
    {
        Schema::table('cuotas', function (Blueprint $table) {
            $table->dropColumn('baja');
        });
    }
}
