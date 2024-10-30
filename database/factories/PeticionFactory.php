<?php

namespace Database\Factories;

use App\Models\Contribuyente;
use App\Models\Peticion;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Peticion>
 */
class PeticionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Peticion::class;

    public function definition()
    {
        return [
            'tipo_peticion' => $this->faker->word,
            'fecha_asignacion' => Carbon::now(),
            'contribuyente_id' => Contribuyente::factory(),
            'funcionario_id' => User::factory(),
            'fecha_vencimiento' => Carbon::now()->addDays($this->faker->numberBetween(1, 30)),
        ];
    }
}

