<?php

use App\Models\MunicipioContacto;
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
        Schema::create('proveedor_municipios', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Proveedor::class);
            $table->foreignIdFor(MunicipioContacto::class);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proveedor_municipios');
    }
};
