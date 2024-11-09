<?php

namespace Database\Seeders;

use App\Models\PaisContacto;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PaisContactoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PaisContacto::create([
            'nombre' => 'México',
            'alias' => 'MX',
        ]);
    }
}
