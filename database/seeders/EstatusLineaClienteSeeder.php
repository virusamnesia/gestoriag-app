<?php

namespace Database\Seeders;

use App\Models\EstatusLineaCliente;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EstatusLineaClienteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        EstatusLineaCliente::create([
            'nombre' => 'Autorizado',
            'alias' => 'AUT',
        ]);
        EstatusLineaCliente::create([
            'nombre' => 'Anticipo',
            'alias' => 'ANT',
        ]);
        EstatusLineaCliente::create([
            'nombre' => 'Recibido',
            'alias' => 'REC',
        ]);
        EstatusLineaCliente::create([
            'nombre' => 'Entregado',
            'alias' => 'ENT',
        ]);
    }
}
