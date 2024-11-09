<?php

use App\Models\MunicipioContacto;
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
        Schema::create('ciudad_contactos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->foreignIdFor(MunicipioContacto::class);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ciudad_contactos');
    }
};
