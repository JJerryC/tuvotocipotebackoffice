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
        Schema::table('candidates', function (Blueprint $table) {
            // Solo agregar campos que NO existen

            // Verificar si fotografia_original no existe
            if (!Schema::hasColumn('candidates', 'fotografia_original')) {
                $table->string('fotografia_original')->nullable()->after('fotografia');
            }

            // Verificar si reeleccion no existe (ya tienes reeleccion como boolean)
            if (!Schema::hasColumn('candidates', 'reeleccion') && Schema::hasColumn('candidates', 'reeleccion')) {
                // Ya existe, no hacer nada
            } elseif (!Schema::hasColumn('candidates', 'reeleccion')) {
                $table->boolean('reeleccion')->default(false)->after('fotografia_original');
            }

            // Verificar si propuestas no existe (ya tienes propuestas como text)
            if (!Schema::hasColumn('candidates', 'propuestas') && Schema::hasColumn('candidates', 'propuestas')) {
                // Ya existe, no hacer nada
            } elseif (!Schema::hasColumn('candidates', 'propuestas')) {
                $table->text('propuestas')->nullable()->after('reeleccion');
            }

            // Campos nuevos para el dashboard
            if (!Schema::hasColumn('candidates', 'tipo_candidato')) {
                $table->enum('tipo_candidato', ['presidencial', 'diputado', 'alcalde'])->nullable()->after('propuestas');
            }

            if (!Schema::hasColumn('candidates', 'genero')) {
                $table->enum('genero', ['masculino', 'femenino'])->nullable()->after('tipo_candidato');
            }

            if (!Schema::hasColumn('candidates', 'independiente')) {
                $table->boolean('independiente')->default(false)->after('genero');
            }

            if (!Schema::hasColumn('candidates', 'porcentaje_completado')) {
                $table->integer('porcentaje_completado')->default(0)->after('independiente');
            }

            if (!Schema::hasColumn('candidates', 'perfil_completo')) {
                $table->boolean('perfil_completo')->default(false)->after('porcentaje_completado');
            }

            // Verificar si entidad_id no existe como foreign key
            if (!Schema::hasColumn('candidates', 'entidad_id')) {
                $table->foreignId('entidad_id')->nullable()->after('party_id')
                      ->constrained('entidades')
                      ->cascadeOnUpdate()
                      ->nullOnDelete();
            }

            // Verificar si departamento_id no existe como foreign key
            if (!Schema::hasColumn('candidates', 'departamento_id')) {
                $table->foreignId('departamento_id')->nullable()->after('municipio_id')
                      ->constrained('departamentos')
                      ->cascadeOnUpdate()
                      ->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('candidates', function (Blueprint $table) {
            // Solo eliminar las columnas que agregamos en esta migración
            $columnsToCheck = [
                'fotografia_original',
                'tipo_candidato',
                'genero',
                'independiente',
                'porcentaje_completado',
                'perfil_completo'
            ];

            foreach ($columnsToCheck as $column) {
                if (Schema::hasColumn('candidates', $column)) {
                    $table->dropColumn($column);
                }
            }

            // Eliminar foreign keys si existen
            if (Schema::hasColumn('candidates', 'entidad_id')) {
                $table->dropForeign(['entidad_id']);
                $table->dropColumn('entidad_id');
            }

            if (Schema::hasColumn('candidates', 'departamento_id')) {
                $table->dropForeign(['departamento_id']);
                $table->dropColumn('departamento_id');
            }
        });
    }
};
