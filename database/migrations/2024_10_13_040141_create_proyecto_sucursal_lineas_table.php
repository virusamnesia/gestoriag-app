<?php

use App\Models\Cliente;
use App\Models\ClientesFactura;
use App\Models\MovimientosPagoCliente;
use App\Models\MovimientosPagoProveedor;
use App\Models\Proveedor;
use App\Models\ProveedorFactura;
use App\Models\ProyectoLinea;
use App\Models\TiposProceso;
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
        Schema::create('proyecto_sucursal_lineas', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(ProyectoLinea::class)->index();
            $table->foreignIdfor(MovimientosPagoCliente::class);
            $table->foreignIdfor(MovimientosPagoProveedor::class);
            $table->foreignIdFor(TiposProceso::class);
            $table->boolean('es_facturable');
            $table->timestamp('fecha_mov');
            $table->foreignIdFor(Cliente::class)->nullable();
            $table->foreignIdFor(Proveedor::class)->nullable();
            $table->timestamp('fecha_factura')->nullable();
            $table->foreignIdFor(ClientesFactura::class)->nullable();
            $table->foreignIdFor(ProveedorFactura::class)->nullable();
            $table->float('importe')->nullable();
            $table->float('saldo')->nullable();
            $table->text('observaciones')->nullable();
            $table->string('url')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proyecto_sucursal_lineas');
    }
};
