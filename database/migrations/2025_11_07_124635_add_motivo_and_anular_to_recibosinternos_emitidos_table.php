<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMotivoAndAnularToRecibosinternosEmitidosTable extends Migration
{
    public function up(): void
    {
        Schema::table('recibosinternos_emitidos', function (Blueprint $table) {
            $table->string('motivo')->nullable()->after('id'); // puedes ajustar el "after" al campo que desees
            $table->string('anular')->nullable()->after('motivo');
        });
    }

    public function down(): void
    {
        Schema::table('recibosinternos_emitidos', function (Blueprint $table) {
            $table->dropColumn(['motivo', 'anular']);
        });
    }
}
