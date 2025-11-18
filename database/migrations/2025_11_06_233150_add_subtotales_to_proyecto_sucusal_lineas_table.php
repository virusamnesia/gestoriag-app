<?php

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
        Schema::table('proyecto_sucursal_lineas', function (Blueprint $table) {
            $table->float('subtotal_cliente')->nullable()->after('saldo_cliente');
            $table->float('subtotal_proveedor')->nullable()->after('saldo_proveedor');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('proyecto_sucursal_lineas', function (Blueprint $table) {
            $table->dropColumn('subtotal_cliente');
            $table->dropColumn('subtotal_proveedor');
        });
    }
};
