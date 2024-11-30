<?php

use App\Models\Cliente;
use App\Models\Proyecto;
use App\Models\Sucursal;
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
        Schema::create('sucursales_proyectos', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Proyecto::class);
            $table->foreignIdFor(Cliente::class);
            $table->foreignIdFor(Sucursal::class);
            $table->boolean('cotizado');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sucursales_proyectos');
    }
};
