<?php

use App\Models\EstatusLineaProveedor;
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
        Schema::table('proyecto_lineas', function (Blueprint $table) {
            $table->foreignIdFor(EstatusLineaProveedor::class)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('proyecto_lineas', function (Blueprint $table) {
            $table->dropColumn('estatus_linea_proveedor_id');
        });
    }
};
