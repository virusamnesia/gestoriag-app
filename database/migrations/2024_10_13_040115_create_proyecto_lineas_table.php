<?php

use App\Models\Cliente;
use App\Models\EstatusLineaCliente;
use App\Models\EstatusLineaProveedor;
use App\Models\Presupuesto;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\Proyecto;
use App\Models\ProyectoSucursalLinea;
use App\Models\Sucursal;
use App\Models\TerminosPagoCliente;
use App\Models\TerminosPagoProveedor;
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
        Schema::create('proyecto_lineas', function (Blueprint $table) {
            $table->id();
            $table->foreignIdfor(Proyecto::class)->index();
            $table->foreignIdFor(Cliente::class);
            $table->foreignIdFor(Sucursal::class);
            $table->foreignIdfor(Producto::class);
            $table->foreignIdFor(Proveedor::class)->nullable();
            $table->foreignIdFor(Presupuesto::class)->nullable();
            $table->float('precio');
            $table->float('saldocliente');
            $table->float('cxc');
            $table->float('costo')->nullable();
            $table->float('saldoproveedor')->nullable();
            $table->float('cxp');
            $table->foreignIdFor(TerminosPagoCliente::class)->nullable();
            $table->foreignIdFor(EstatusLineaCliente::class)->nullable();
            $table->foreignIdFor(ProyectoSucursalLinea::class)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proyecto_lineas');
    }
};
