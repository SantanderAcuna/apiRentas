<?php

namespace Database\Seeders;

use App\Models\Peticion;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PeticionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Peticion::factory()->count(10)->create();
    }
}
