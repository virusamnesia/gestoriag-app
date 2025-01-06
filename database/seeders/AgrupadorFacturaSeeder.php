<?php

namespace Database\Seeders;

use App\Models\AgrupadorFactura;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AgrupadorFacturaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AgrupadorFactura::create([
            'nombre' => 'CONTRATOS DE SERVICIOS BASICOS',
        ]);

        AgrupadorFactura::create([
            'nombre' => 'TRAMITES Y PERMISOS',
        ]);

        AgrupadorFactura::create([
            'nombre' => 'DICTAMENES',
        ]);

        AgrupadorFactura::create([
            'nombre' => 'SEÑALETICA, EXTINTORES Y EQUIPO CONTRA INCENDIO',
        ]);

        AgrupadorFactura::create([
            'nombre' => 'BOTIQUIN DE PRIMEROS AUXILIOS Y SUMINISTROS',
        ]);

        AgrupadorFactura::create([
            'nombre' => ' MANTENIMIENTO Y RECARGA DE EXTINTORES',
        ]);

        AgrupadorFactura::create([
            'nombre' => 'SISTEMAS DE ALARMAS E INSTALACION',
        ]);
    }
}
