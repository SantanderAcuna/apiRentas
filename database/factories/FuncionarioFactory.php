<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Funcionario>
 */
class FuncionarioFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // Genera un valor único para la cédula (simula números largos)
            'cedula' => $this->faker->unique()->numberBetween(1000000000, 9999999999),

            // Genera un nombre ficticio
            'nombre' => $this->faker->name,

            // Genera un correo electrónico ficticio
            'email' => $this->faker->unique()->safeEmail,

            // Define un ID de asignado; este ID debería existir en la tabla `asignacions` para evitar errores
            'asignado_id' => $this->faker->numberBetween(1, 3), // Ajusta el rango según tus datos

            // Genera un área ficticia
            'area' => $this->faker->word,

            // Genera un valor aleatorio (true/false) para indicar si es líder de área
            'lider_area' => $this->faker->boolean,

            // Genera un valor aleatorio (true/false) para indicar si es director
            'director' => $this->faker->boolean,
        ];
    }
}
