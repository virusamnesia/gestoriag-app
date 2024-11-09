<?php

namespace Database\Seeders;

use App\Models\EstadosPresupuesto;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EstadosPresupuestoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        EstadosPresupuesto::create([
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
