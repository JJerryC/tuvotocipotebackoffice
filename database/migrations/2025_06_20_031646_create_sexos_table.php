<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up(): void
{
    Schema::create('sexos', function (Blueprint $table) {
        $table->id();
        $table->char('code', 1)->unique();    
        $table->string('description');
        $table->timestamps();
    });

    DB::table('sexos')->insert([
        ['code' => 'M', 'description' => 'Masculino'],
        ['code' => 'F', 'description' => 'Femenino'],
        ['code' => 'O', 'description' => 'Otro'],
    ]);
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sexos');
    }
};
