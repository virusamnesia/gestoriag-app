<?php

use App\Models\Cliente;
use App\Models\ImportacionProyecto;
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
        Schema::create('importacions', function (Blueprint $table) {
            $table->id();
            $table->string('file');
            $table->foreignIdFor(Proyecto::class);
            $table->foreignIdFor(Cliente::class);
            $table->foreignIdFor(ImportacionProyecto::class);
            $table->boolean('es_procesado');
            $table->timestamp('fecha');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('importacions');
    }
};
