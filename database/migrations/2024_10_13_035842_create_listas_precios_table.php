<?php

use App\Models\Cliente;
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
        Schema::create('listas_precios', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('alias', 5);
            $table->foreignIdFor(Cliente::class)->nullable();
            $table->timestamp('inicio')->nullable();
            $table->timestamp('final')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('listas_precios');
    }
};
