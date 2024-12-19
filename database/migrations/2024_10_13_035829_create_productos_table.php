<?php

use App\Models\TerminosPagoCliente;
use App\Models\TerminosPagoProveedor;
use App\Models\TiposProducto;
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
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('alias', 5)->nullable();
            $table->foreignIdFor(TerminosPagoCliente::class)->nullable();
            $table->foreignIdFor(TiposProducto::class);
            $table->boolean('es_activo');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
