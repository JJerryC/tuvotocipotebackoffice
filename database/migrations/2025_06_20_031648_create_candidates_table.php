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
    Schema::create('candidates', function (Blueprint $table) {
        $table->id();

        $table->foreignId('party_id')
              ->constrained('parties')
              ->cascadeOnUpdate()
              ->restrictOnDelete();

        $table->foreignId('nomina_id')
              ->constrained('nominas')
              ->cascadeOnUpdate()
              ->restrictOnDelete();

        $table->foreignId('municipio_id')
              ->constrained('municipios')
              ->cascadeOnUpdate()
              ->restrictOnDelete();

        $table->foreignId('cargo_id')
              ->constrained('cargos')
              ->cascadeOnUpdate()
              ->restrictOnDelete();

        $table->foreignId('sexo_id')
              ->constrained('sexos')
              ->cascadeOnUpdate()
              ->restrictOnDelete();

        $table->unsignedInteger('posicion')->comment('Posición en la papeleta/lista');
        $table->string('numero_identidad', 25)->unique();

      
        $table->string('primer_nombre', 60);
        $table->string('segundo_nombre', 60)->nullable();
        $table->string('primer_apellido', 60);
        $table->string('segundo_apellido', 60)->nullable();

        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('candidates');
    }
};
