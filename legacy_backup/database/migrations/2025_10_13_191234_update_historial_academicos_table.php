<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateHistorialAcademicosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('historial_academicos', function (Blueprint $table) {
            // Actualizar llave foránea existente
            $table->dropForeign(['estudiante_id']);
            $table->foreign('estudiante_id')
                  ->references('id')->on('estudiantes')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');

            // Nuevos campos
            $table->foreignId('nivel_id')->nullable()
                  ->constrained('niveles')
                  ->onDelete('set null');

            $table->foreignId('grado_id')->nullable()
                  ->constrained('tb_grados')
                  ->onDelete('set null');

            $table->foreignId('curso_id')->nullable()
                  ->constrained('cursos')
                  ->onDelete('set null');

            $table->year('año')->nullable();
            $table->string('establecimiento')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('historial_academicos', function (Blueprint $table) {
            // Revertir cambios
            $table->dropForeign(['nivel_id']);
            $table->dropForeign(['grado_id']);
            $table->dropForeign(['curso_id']);

            $table->dropColumn(['nivel_id', 'grado_id', 'curso_id', 'año', 'establecimiento']);

            // Revertir la relación estudiante_id sin onUpdate
            $table->dropForeign(['estudiante_id']);
            $table->foreign('estudiante_id')
                  ->references('id')->on('estudiantes')
                  ->onDelete('cascade');
        });
    }
}
