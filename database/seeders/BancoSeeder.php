<?php

namespace Database\Seeders;

use App\Models\Banco;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BancoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Banco::create([
            'nombre' => 'AFIRME',
        ]);
    
        Banco::create([
            'nombre' => 'AZTECA',
        ]);
    
        Banco::create([
            'nombre' => 'BAJIO',
        ]);
    
        Banco::create([
            'nombre' => 'BANAMEX',
        ]);
    
        Banco::create([
            'nombre' => 'BANORTE/IXE',
        ]);
    
        Banco::create([
            'nombre' => 'BANREGIO',
        ]);
    
        Banco::create([
            'nombre' => 'BBVA BANCOMER',
        ]);
    
        Banco::create([
            'nombre' => 'HSBC',
        ]);
    
        Banco::create([
            'nombre' => 'INBURSA',
        ]);
    
        Banco::create([
            'nombre' => 'MIFEL',
        ]);
    
        Banco::create([
            'nombre' => 'SANTANDER',
        ]);
    
        Banco::create([
            'nombre' => 'SCOTIABANK',
        ]);
    }
}
