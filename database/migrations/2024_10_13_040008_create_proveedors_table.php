<?php

use App\Models\Banco;
use App\Models\CiudadContacto;
use App\Models\EstadoContacto;
use App\Models\MunicipioContacto;
use App\Models\PaisContacto;
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
        Schema::create('proveedors', function (Blueprint $table) {
            $table->id();
            $table->string('clave');
            $table->string('nombre');
            $table->string('rfc')->unique();
            $table->string('domicilio');
            $table->string('colonia')->nullable();
            $table->foreignIdFor(CiudadContacto::class);
            $table->foreignIdFor(MunicipioContacto::class);
            $table->foreignIdFor(EstadoContacto::class);
            $table->foreignIdFor(PaisContacto::class);
            $table->string('cp', 5);
            $table->string('email')->nullable();
            $table->string('telefono')->nullable();
            $table->string('cuenta')->nullable();
            $table->foreignIdFor(Banco::class)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proveedors');
    }
};
