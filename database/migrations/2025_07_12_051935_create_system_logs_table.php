<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('system_logs', function (Blueprint $table) {
            $table->id();

            // Modelo afectado (morph)
            $table->string('model_type');
            $table->unsignedBigInteger('model_id')->nullable();

            // Contexto de la tabla y acción
            $table->string('table_name');
            $table->string('action', 20);
            $table->json('changes')->nullable();

            // Usuario que realiza la acción
            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();
            $table->string('user_name')->nullable();

            // Metadatos extras
            $table->string('module')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_logs');
    }
};
