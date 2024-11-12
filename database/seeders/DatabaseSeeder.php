<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Asignacion;
use App\Models\Gestiona;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            AsignacionSeeder::class,
            FuncionarioSeeder::class,
            ContribuyenteSeeder::class,
            PeticionSeeder::class,
            NotificacionSeeder::class,

        ]);
    }
}
