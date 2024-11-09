<?php

use App\Models\Cliente;
use App\Models\EstadosProyecto;
use App\Models\ListasPrecio;
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
        Schema::create('proyectos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->integer('anio');
            $table->foreignIdFor(Cliente::class)->index();
            $table->float('importe');
            $table->float('saldo');
            $table->foreignIdFor(EstadosProyecto::class);
            $table->foreignIdfor(ListasPrecio::class);
            $table->timestamp('fecha_cotizacion')->nullable();
            $table->timestamp('fecha_autorizacion')->nullable();
            $table->timestamp('fecha_finalizacion')->nullable();
            $table->timestamp('fecha_cancelacion')->nullable();
            $table->boolean('es_agrupado');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proyectos');
    }
};
