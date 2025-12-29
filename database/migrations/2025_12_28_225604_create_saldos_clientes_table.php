<?php

use App\Models\Cliente;
use App\Models\ClientesFactura;
use App\Models\Producto;
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
        Schema::create('saldos_clientes', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Proyecto::class);
            $table->foreignIdFor(Cliente::class);
            $table->foreignIdFor(Sucursal::class);
            $table->foreignIdFor(Producto::class);
            $table->float('subtotal');
            $table->float('iva_t');
            $table->float('isr_r');
            $table->float('iva_r');
            $table->float('imp_c');
            $table->float('total');
            $table->float('saldo');
            $table->float('aplicado');
            $table->foreignIdFor(ClientesFactura::class)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('saldos_clientes');
    }
};
