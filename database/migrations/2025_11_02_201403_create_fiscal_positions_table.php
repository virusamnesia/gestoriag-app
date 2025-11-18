<?php

use App\Models\RegimenesFiscale;
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
        Schema::create('fiscal_positions', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->foreignIdFor(RegimenesFiscale::class)->nullable();
            $table->float('iva_t');
            $table->float('isr_r');
            $table->float('iva_r');
            $table->float('imp_c');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fiscal_positions');
    }
};
