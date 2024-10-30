<?php

namespace Database\Factories;

use App\Models\Contribuyente;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Contribuyente>
 */
class ContribuyenteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Contribuyente::class;



    public function definition(): array
    {
        return [
            'cedula' => $this->faker->unique()->numerify('##########'),
            'nombre_completo' => $this->faker->name,
            'ref_catastral' => $this->faker->optional()->numerify('CATA-####-###'),
            'email' => $this->faker->optional()->email,
        ];
    }
}
