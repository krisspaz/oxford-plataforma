<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 🧩 Tabla personas: hacer que 'profesion' sea nullable
        Schema::table('personas', function (Blueprint $table) {
            $table->string('profesion')->nullable()->change();
        });

        // 🎓 Tabla estudiantes: hacer que 'cgshges_id' sea nullable
        Schema::table('estudiantes', function (Blueprint $table) {
            $table->unsignedBigInteger('cgshges_id')->nullable()->change();
        });

        // 👨‍🏫 Tabla docentes: agregar 'cedula' nullable y hacer 'especialidad' nullable
        Schema::table('docentes', function (Blueprint $table) {
            if (!Schema::hasColumn('docentes', 'cedula')) {
                $table->string('cedula')->nullable()->after('id');
            }

            $table->string('especialidad')->nullable()->change();
        });

        // 🧮 Tabla pv_cgshges: agregar 'grado2_id' tipo JSON nullable
        Schema::table('pv_cgshges', function (Blueprint $table) {
            if (!Schema::hasColumn('pv_cgshges', 'grado2_id')) {
                $table->json('grado2_id')->nullable()->after('id');
            }
        });

        // 📊 Tabla bimestres: agregar 'porcentaje' decimal nullable
        Schema::table('bimestres', function (Blueprint $table) {
            if (!Schema::hasColumn('bimestres', 'porcentaje')) {
                $table->decimal('porcentaje', 5, 2)->nullable()->after('id');
            }
        });
    }

    public function down(): void
    {
        // Revertir cambios
        Schema::table('personas', function (Blueprint $table) {
            $table->string('profesion')->nullable(false)->change();
        });

        Schema::table('estudiantes', function (Blueprint $table) {
            $table->unsignedBigInteger('cgshges_id')->nullable(false)->change();
        });

        Schema::table('docentes', function (Blueprint $table) {
            if (Schema::hasColumn('docentes', 'cedula')) {
                $table->dropColumn('cedula');
            }
            $table->string('especialidad')->nullable(false)->change();
        });

        Schema::table('pv_cgshges', function (Blueprint $table) {
            if (Schema::hasColumn('pv_cgshges', 'grado2_id')) {
                $table->dropColumn('grado2_id');
            }
        });

        Schema::table('bimestres', function (Blueprint $table) {
            if (Schema::hasColumn('bimestres', 'porcentaje')) {
                $table->dropColumn('porcentaje');
            }
        });
    }
};
