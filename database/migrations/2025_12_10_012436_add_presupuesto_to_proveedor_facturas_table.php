<?php

use App\Models\Presupuesto;
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
        Schema::table('proveedor_facturas', function (Blueprint $table) {
            $table->foreignIdFor(Presupuesto::class)->nullable()->after('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('proveedor_facturas', function (Blueprint $table) {
            $table->dropColumn('presupuesto_id');
        });
    }
};
