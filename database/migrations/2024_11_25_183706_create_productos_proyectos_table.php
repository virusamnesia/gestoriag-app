<?php

use App\Models\Producto;
use App\Models\Proyecto;
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
        Schema::create('productos_proyectos', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Proyecto::class);
            $table->foreignIdFor(Producto::class);
            $table->boolean('cotizado');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productos_proyectos');
    }
};
