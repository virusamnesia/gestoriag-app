<?php

use App\Models\Cliente;
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
        Schema::create('clientes_facturas', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Proyecto::class)->index();
            $table->foreignIdFor(Cliente::class);
            $table->float('subtotal');
            $table->float('impuestos');
            $table->float('total');
            $table->timestamp('fecha')->nullable();
            $table->boolean('es_activo');
            $table->string('factura_odoo')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clientes_facturas');
    }
};
