<?php

namespace Database\Seeders;

use App\Models\EstadosProyecto;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EstadosProyectoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        EstadosProyecto::create([
            'nombre' => 'Cotización',
        ]);
    
        EstadosProyecto::create([
            'nombre' => 'Autorizado',
        ]);
        
        EstadosProyecto::create([
            'nombre' => 'Recibido',
        ]);
    
        EstadosProyecto::create([
            'nombre' => 'Entregado',
        ]);
    
        EstadosProyecto::create([
            'nombre' => 'Finalizado',
        ]);
    
        EstadosProyecto::create([
            'nombre' => 'Cancelado',
        ]);
    }
}
