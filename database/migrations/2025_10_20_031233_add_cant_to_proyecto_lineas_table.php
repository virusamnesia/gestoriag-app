<?php

use App\Models\FiscalPosition;
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
            $table->integer('cantidad')->nullable()->after('presupuesto_id');
            $table->float('subtotal_v')->nullable()->after('precio');
            $table->float('iva_t_v')->nullable()->after('subtotal_v');
            $table->float('isr_r_v')->nullable()->after('iva_t_v');
            $table->float('iva_r_v')->nullable()->after('isr_r_v');
            $table->float('imp_c_v')->nullable()->after('iva_r_v');
            $table->float('total_v')->nullable()->after('imp_c_v');
            $table->float('subtotal_c')->nullable()->after('costo');
            $table->float('iva_t_c')->nullable()->after('subtotal_c');
            $table->float('isr_r_c')->nullable()->after('iva_t_c');
            $table->float('iva_r_c')->nullable()->after('isr_r_c');
            $table->float('imp_c_c')->nullable()->after('iva_r_c');
            $table->float('total_c')->nullable()->after('imp_c_c');
            $table->foreignIdFor(FiscalPosition::class)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('proyecto_lineas', function (Blueprint $table) {
            $table->dropColumn('cantidad');
            $table->dropColumn('subtotal_v');
            $table->dropColumn('iva_t_v');
            $table->dropColumn('isr_r_v');
            $table->dropColumn('iva_r_v');
            $table->dropColumn('imp_c_v');
            $table->dropColumn('total_v');
            $table->dropColumn('subtotal_c');
            $table->dropColumn('iva_t_c');
            $table->dropColumn('isr_r_c');
            $table->dropColumn('iva_r_c');
            $table->dropColumn('imp_c_c');
            $table->dropColumn('total_c');
        });
    }
};
