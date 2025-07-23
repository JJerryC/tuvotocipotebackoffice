<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('planillas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre'); // Nombre de la planilla
            $table->string('foto')->nullable(); // Imagen de la planilla
            $table->foreignId('cargo_id')->constrained('cargos')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('departamento_id')->nullable()->constrained('departamentos')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('municipio_id')->nullable()->constrained('municipios')->cascadeOnUpdate()->restrictOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('planillas');
    }
};