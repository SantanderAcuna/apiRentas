<?php

namespace Database\Seeders;

use App\Models\Asignacion;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AsignacionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $nombres = ['Desembargo', 'Prescripcion', 'Exoneracion'];
        
        foreach ($nombres as $nombre) {
            Asignacion::factory()->create([
                'nombre' => $nombre,
              
            ]);
        }
    }
}