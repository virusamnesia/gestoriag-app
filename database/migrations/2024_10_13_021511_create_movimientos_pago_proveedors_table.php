<?php

use App\Models\EstatusLineaProveedor;
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
        Schema::create('movimientos_pago_proveedors', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(TerminosPagoProveedor::class);
            $table->integer('secuencia');
            $table->string('nombre');
            $table->foreignIdFor(EstatusLineaProveedor::class);
            $table->boolean('facturable');
            $table->float('porcentaje');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movimientos_pago_proveedors');
    }
};
