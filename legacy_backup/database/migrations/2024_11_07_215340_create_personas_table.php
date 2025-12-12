<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePersonasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
       
        Schema::create('personas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parentesco_id')->nullable()->constrained('tb_parentescos');
            $table->string('nombres');
            $table->string('apellidos');
            $table->enum('genero', ['Masculino', 'Femenino']);
            $table->string('estado_civil')->nullable();
            $table->string('apellido_casada')->nullable();
            $table->foreignId('identificacion_documentos_id')->constrained('tb_identificacion_documentos');
            $table->string('num_documento')->unique()->nullable();
            $table->string('profesion');
            $table->date('fecha_nacimiento');
           
            $table->string('email')->unique()->nullable();
            $table->string('telefono')->nullable();
            $table->string('direccion')->nullable();
            $table->date('fecha_defuncion')->nullable();  // Cambié a nullable si puede no tener fecha de defunción
            $table->unsignedInteger('cms_users_id')->nullable();
            $table->foreign('cms_users_id')->references('id')->on('cms_users')->onDelete('set null');
            $table->timestamps();  // Agrega timestamps si es necesario


        });
        
        
        
   
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('personas');
    }
}
