<?php

use App\Models\Cliente;
use App\Models\Importacion;
use App\Models\Proyecto;
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
        Schema::create('importacion_errors', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Importacion::class);
            $table->text('mensaje');
            $table->timestamp('fecha');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('importacion_errors');
    }
};
