<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use function Laravel\Prompts\table;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tipos_procesos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->boolean('es_proceso');
            $table->boolean('es_config');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tipos_procesos');
    }
};
