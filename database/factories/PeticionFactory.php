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
            'tipo_peticion' => $this->faker->randomElement(['Desembargo', 'Prescripcion', 'Exoneracion']),
            'fecha_asignacion' => $this->faker->dateTimeBetween('now'),
            'contribuyente_id' => $this->faker->numberBetween(1, 10),
            'funcionario_id' => $this->faker->numberBetween(1, 10),
            'fecha_vencimiento' => Carbon::now()->addDays(15)->format('Y-m-d'),
        ];
    }
}




