<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            // Agregar columna categoria si no existe (ya incluida en creación)
            if (!Schema::hasColumn('productos', 'categoria')) {
                $table->string('categoria')->nullable()->after('descripcion');
            }
        });
    }

    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            if (Schema::hasColumn('productos', 'categoria')) {
                $table->dropColumn('categoria');
            }
        });
    }
};
