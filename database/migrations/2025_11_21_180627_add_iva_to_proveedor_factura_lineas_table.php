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
        Schema::table('proveedor_factura_lineas', function (Blueprint $table) {
            $table->float('iva_t')->nullable()->after('subtotal');
            $table->float('isr_r')->nullable()->after('iva_t');
            $table->float('iva_r')->nullable()->after('isr_r');
            $table->float('imp_c')->nullable()->after('iva_r');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('proveedor_factura_lineas', function (Blueprint $table) {
            $table->dropColumn('iva_t');
            $table->dropColumn('isr_r');
            $table->dropColumn('iva_r');
            $table->dropColumn('imp_c');
        });
    }
};
