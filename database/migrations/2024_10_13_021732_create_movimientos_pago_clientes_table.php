<?php

use App\Models\EstatusLineaCliente;
use App\Models\TerminosPagoCliente;
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
        Schema::create('movimientos_pago_clientes', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(TerminosPagoCliente::class);
            $table->integer('secuencia');
            $table->foreignIdFor(EstatusLineaCliente::class);
            $table->float('valor_cliente')->nullable();
            $table->float('valor_proveedor')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movimientos_pago_clientes');
    }
};
