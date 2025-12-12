<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePagoMetodosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pago_metodos', function (Blueprint $table) {
            $table->foreignId('pago_id')->constrained()->onDelete('cascade'); // Relación con pagos
            $table->enum('metodo_pago', ['efectivo', 'transferencia', 'tarjeta', 'cheque', 'deposito']);
            $table->decimal('monto', 10, 2);
            $table->primary(['pago_id', 'metodo_pago']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pago_metodos');
    }
}
