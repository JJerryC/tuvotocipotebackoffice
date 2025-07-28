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
    Schema::create('parties', function (Blueprint $table) {
        $table->id();
        $table->string('name')->unique();
        $table->string('foto_partido')->nullable();
        $table->string('color_partido')->nullable(); // <- Nuevo campo
        $table->text('descripcion')->nullable();     // <- Nuevo campo
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parties');
    }
};
