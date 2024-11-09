<?php

use App\Models\EstadosPresupuesto;
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
        Schema::create('presupuestos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->integer('anio');
            $table->foreignIdFor(Proveedor::class)->index();
            $table->float('importe');
            $table->float('saldo');
            $table->foreignIdFor(EstadosPresupuesto::class);
            $table->timestamp('fecha_cotizacion')->nullable();
            $table->timestamp('fecha_autorizacion')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('presupuestos');
    }
};
