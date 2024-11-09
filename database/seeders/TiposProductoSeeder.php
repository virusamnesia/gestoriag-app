<?php

namespace Database\Seeders;

use App\Models\TiposProducto;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TiposProductoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TiposProducto::create([
            'nombre' => 'CFE',
            'alias' => 'CFE',
            'es_activo' => True,
        ]);
    
        TiposProducto::create([
            'nombre' => 'DESARROLLO URBANO',
            'alias' => 'DURB',
            'es_activo' => True,
        ]);

        TiposProducto::create([
            'nombre' => 'ECOLOGIA',
            'alias' => 'ECO',
            'es_activo' => True,
        ]);

        TiposProducto::create([
            'nombre' => 'PROTECCION CIVIL',
            'alias' => 'PRCIV',
            'es_activo' => True,
        ]);

        TiposProducto::create([
            'nombre' => 'DICTAMENES',
            'alias' => 'DICT',
            'es_activo' => True,
        ]);

        TiposProducto::create([
            'nombre' => 'FISCALIZACIÓN Y COFEPRIS',
            'alias' => 'FICOF',
            'es_activo' => True,
        ]);

        TiposProducto::create([
            'nombre' => 'CANACO',
            'alias' => 'CNCO',
            'es_activo' => True,
        ]);

        TiposProducto::create([
            'nombre' => 'AGUA',
            'alias' => 'AGUA',
            'es_activo' => True,
        ]);

        TiposProducto::create([
            'nombre' => 'PRODUCTO',
            'alias' => 'PROD',
            'es_activo' => True,
        ]);
    }
}
