<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePagoCuotasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pago_cuotas', function (Blueprint $table) {
           
            $table->foreignId('pago_id')->constrained()->onDelete('cascade'); // Relación con pagos
            $table->foreignId('cuota_id')->constrained()->onDelete('cascade'); // Relación con cuotas
            $table->primary(['pago_id', 'cuota_id']);
           
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pago_cuotas');
    }
}
