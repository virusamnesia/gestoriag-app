<?php

use App\Models\ImportacionProyecto;
use App\Models\Producto;
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
        Schema::create('importacion_proyecto_productos', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(ImportacionProyecto::class);
            $table->foreignIdFor(Producto::class);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('importacion_proyecto_productos');
    }
};
