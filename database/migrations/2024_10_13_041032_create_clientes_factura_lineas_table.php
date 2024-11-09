<?php

use App\Models\ClientesFactura;
use App\Models\ProyectoSucursalLinea;
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
        Schema::create('clientes_factura_lineas', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(ClientesFactura::class);
            $table->foreignIdFor(ProyectoSucursalLinea::class)->nullable();
            $table->float('subtotal');
            $table->float('impuestos');
            $table->float('total');
            $table->timestamp('fecha')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clientes_factura_lineas');
    }
};
