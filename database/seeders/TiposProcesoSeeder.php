<?php

namespace Database\Seeders;

use App\Models\TiposProceso;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TiposProcesoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TiposProceso::create([
            'nombre' => 'Cliente',
            'es_proceso' => True,
            'es_config' => False,
        ]);
    
        TiposProceso::create([
            'nombre' => 'Proveedor',
            'es_proceso' => True,
            'es_config' => False,
        ]);
    }
}
