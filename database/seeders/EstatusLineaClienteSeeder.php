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
            'nombre' => 'Cotizado',
            'alias' => 'COT',
        ]);
        EstatusLineaCliente::create([
            'nombre' => 'Anticipo',
            'alias' => 'ANT',
        ]);
        EstatusLineaCliente::create([
            'nombre' => 'Finiquito',
            'alias' => 'FIN',
        ]);
        EstatusLineaCliente::create([
            'nombre' => 'Total',
            'alias' => 'TOT',
        ]);
        EstatusLineaCliente::create([
            'nombre' => 'Avance 1',
            'alias' => 'AV1',
        ]);
        EstatusLineaCliente::create([
            'nombre' => 'Avance 2',
            'alias' => 'AV2',
        ]);
    }
}
