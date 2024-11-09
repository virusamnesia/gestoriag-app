<?php

use App\Models\ListasPrecio;
use App\Models\MunicipioContacto;
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
        Schema::create('listas_precio_lineas', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(ListasPrecio::class);
            $table->foreignIdFor(Producto::class);
            $table->foreignIdFor(MunicipioContacto::class);
            $table->float('precio')->nullable();
            $table->float('costo')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('listas_precio_lineas');
    }
};
