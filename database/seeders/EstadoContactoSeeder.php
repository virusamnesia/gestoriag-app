<?php

namespace Database\Seeders;

use App\Models\EstadoContacto;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EstadoContactoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        EstadoContacto::create([
            'nombre' => 'Aguascalientes',
            'alias' => 'AGS',
            'pais_contacto_id' => 1,
        ]);
        EstadoContacto::create([
            'nombre' => 'Baja California',
            'alias' => 'BCN',
            'pais_contacto_id' => 1,
        ]);
        EstadoContacto::create([
            'nombre' => 'Baja California Sur',
            'alias' => 'BCS',
            'pais_contacto_id' => 1,
        ]);
    
        EstadoContacto::create([
            'nombre' => 'Campeche',
            'alias' => 'CAM',
            'pais_contacto_id' => 1,
        ]);
    
        EstadoContacto::create([
            'nombre' => 'Chiapas',
            'alias' => 'CHP',
            'pais_contacto_id' => 1,
        ]);
    
        EstadoContacto::create([
            'nombre' => 'Chihuahua',
            'alias' => 'CHH',
            'pais_contacto_id' => 1,
        ]);
    
        EstadoContacto::create([
            'nombre' => 'Ciudad de México',
            'alias' => 'CMX',
            'pais_contacto_id' => 1,
        ]);
    
        EstadoContacto::create([
            'nombre' => 'Coahuila',
            'alias' => 'COA',
            'pais_contacto_id' => 1,
        ]);
    
        EstadoContacto::create([
            'nombre' => 'Colima',
            'alias' => 'COL',
            'pais_contacto_id' => 1,
        ]);
    
        EstadoContacto::create([
            'nombre' => 'Durango',
            'alias' => 'DUR',
            'pais_contacto_id' => 1,
        ]);
    
        EstadoContacto::create([
            'nombre' => 'Estado de México',
            'alias' => 'MEX',
            'pais_contacto_id' => 1,
        ]);
    
        EstadoContacto::create([
            'nombre' => 'Guanajuato',
            'alias' => 'GUA',
            'pais_contacto_id' => 1,
        ]);
    
        EstadoContacto::create([
            'nombre' => 'Guerrero',
            'alias' => 'GRO',
            'pais_contacto_id' => 1,
        ]);
    
        EstadoContacto::create([
            'nombre' => 'Hidalgo',
            'alias' => 'HID',
            'pais_contacto_id' => 1,
        ]);
    
        EstadoContacto::create([
            'nombre' => 'Jalisco',
            'alias' => 'JAL',
            'pais_contacto_id' => 1,
        ]);
    
        EstadoContacto::create([
            'nombre' => 'Michoacán',
            'alias' => 'MIC',
            'pais_contacto_id' => 1,
        ]);
    
        EstadoContacto::create([
            'nombre' => 'Morelos',
            'alias' => 'MOR',
            'pais_contacto_id' => 1,
        ]);
    
        EstadoContacto::create([
            'nombre' => 'Nayarit',
            'alias' => 'NAY',
            'pais_contacto_id' => 1,
        ]);
    
        EstadoContacto::create([
            'nombre' => 'Nuevo León',
            'alias' => 'NLE',
            'pais_contacto_id' => 1,
        ]);
    
        EstadoContacto::create([
            'nombre' => 'Oaxaca',
            'alias' => 'OAX',
            'pais_contacto_id' => 1,
        ]);
    
        EstadoContacto::create([
            'nombre' => 'Puebla',
            'alias' => 'PUE',
            'pais_contacto_id' => 1,
        ]);
    
        EstadoContacto::create([
            'nombre' => 'Querétaro',
            'alias' => 'QUE',
            'pais_contacto_id' => 1,
        ]);
    
        EstadoContacto::create([
            'nombre' => 'Quintana Roo',
            'alias' => 'ROO',
            'pais_contacto_id' => 1,
        ]);
    
        EstadoContacto::create([
            'nombre' => 'San Luis Potosí',
            'alias' => 'SNL',
            'pais_contacto_id' => 1,
        ]);
    
        EstadoContacto::create([
            'nombre' => 'Sinaloa',
            'alias' => 'SIN',
            'pais_contacto_id' => 1,
        ]);
    
        EstadoContacto::create([
            'nombre' => 'Sonora',
            'alias' => 'SON ',
            'pais_contacto_id' => 1,
        ]);
    
        EstadoContacto::create([
            'nombre' => 'Tabasco',
            'alias' => 'TAB',
            'pais_contacto_id' => 1,
        ]);
    
        EstadoContacto::create([
            'nombre' => 'Tamaulipas',
            'alias' => 'TAM',
            'pais_contacto_id' => 1,
        ]);
    
        EstadoContacto::create([
            'nombre' => 'Tlaxcala',
            'alias' => 'TLA',
            'pais_contacto_id' => 1,
        ]);
    
        EstadoContacto::create([
            'nombre' => 'Veracruz',
            'alias' => 'VER',
            'pais_contacto_id' => 1,
        ]);
    
        EstadoContacto::create([
            'nombre' => 'Yucatán',
            'alias' => 'YUC',
            'pais_contacto_id' => 1,
        ]);
    
        EstadoContacto::create([
            'nombre' => 'Zacatecas',
            'alias' => 'ZAC',
            'pais_contacto_id' => 1,
        ]);
    }
}