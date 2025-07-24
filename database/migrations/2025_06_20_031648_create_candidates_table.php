<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('candidates', function (Blueprint $table) {
            $table->id();

            $table->foreignId('party_id')
                  ->nullable()
                  ->constrained('parties')
                  ->cascadeOnUpdate()
                  ->restrictOnDelete();

            $table->foreignId('entidad_id')
                  ->nullable()
                  ->constrained('entidades')
                  ->cascadeOnUpdate()
                  ->restrictOnDelete();

            $table->foreignId('nomina_id')
                  ->constrained('nominas')
                  ->cascadeOnUpdate()
                  ->restrictOnDelete();

            $table->foreignId('departamento_id')
                  ->constrained('departamentos')
                  ->cascadeOnUpdate()
                  ->restrictOnDelete();

            $table->foreignId('municipio_id')
                  ->nullable()
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

            $table->foreignId('planilla_id')
                  ->nullable()
                  ->constrained('planillas')
                  ->cascadeOnUpdate()
                  ->restrictOnDelete();

            $table->unsignedInteger('posicion')->comment('Posición en la papeleta/lista');
            $table->string('numero_identidad', 25)->unique();

            $table->string('primer_nombre', 60);
            $table->string('segundo_nombre', 60)->nullable();
            $table->string('primer_apellido', 60);
            $table->string('segundo_apellido', 60)->nullable();

            $table->string('fotografia')->nullable()->comment('Ruta o nombre del archivo de la foto');
            $table->boolean('reeleccion')->default(false)->comment('¿Candidato a reelección?');
            $table->text('propuestas')->nullable()->comment('Planes y propuestas del candidato');
            $table->boolean('independiente')->default(false)->comment('Indica si el candidato es independiente');
            $table->string('fotografia_original')->nullable();
            $table->string('ocupacion', 100)->nullable()->comment('Ocupación del candidato');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('candidates');
    }
};
