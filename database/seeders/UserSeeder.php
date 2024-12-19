<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
		
		User::create([
            'name' => 'Miguel Gonzalez',
            'email' => 'mgonzalez@gestoriag.com',
            'password' =>  bcrypt('lmgonzalez'),
        ]);
		
		User::create([
            'name' => 'Gerardo Lopez',
            'email' => 'glopez@negociodigital.me',
            'password' =>  bcrypt('negocio2024'),
        ]);
    }
}
