<?php

use App\Models\Proveedor;
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
        Schema::create('proveedor_facturas', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Proveedor::class)->index();
            $table->float('subtotal');
            $table->float('impuestos');
            $table->float('total');
            $table->timestamp('fecha')->nullable();
            $table->boolean('es_activo');
            $table->boolean('es_factura_odoo');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proveedor_facturas');
    }
};
