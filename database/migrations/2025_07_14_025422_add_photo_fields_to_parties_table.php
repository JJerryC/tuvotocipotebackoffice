<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('parties', function (Blueprint $table) {
            $table->string('foto_partido')->nullable()->after('name');
            $table->string('color_partido', 7)->nullable()->after('foto_partido'); // Para colores hex
            $table->text('descripcion')->nullable()->after('color_partido');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('parties', function (Blueprint $table) {
            $table->dropColumn(['foto_partido', 'color_partido', 'descripcion']);
        });
    }
};
