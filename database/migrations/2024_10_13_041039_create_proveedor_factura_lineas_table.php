<?php

use App\Models\ProveedorFactura;
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
        Schema::create('proveedor_factura_lineas', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(ProveedorFactura::class);
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
        Schema::dropIfExists('proveedor_factura_lineas');
    }
};
