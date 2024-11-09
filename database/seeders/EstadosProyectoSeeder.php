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
            'name' => 'Cotización',
        ],
    
        [
            'name' => 'Autorizado',
        ],
        
        [
            'name' => 'Recibido',
        ],
    
        [
            'name' => 'Entregado',
        ],
    
        [
            'name' => 'Finalizado',
        ],
    
        [
            'name' => 'Cancelado',
        ]);
    }
}
