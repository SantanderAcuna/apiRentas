<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = User::class;

    public function definition()
    {
        return [
            'nombre_completo' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'numero_cedula' => $this->faker->unique()->numerify('##########'),
            'password' => $this->faker->numerify('##########'),
            'area' => $this->faker->randomElement(['Finanzas', 'Recursos Humanos', 'Legal']),
            'es_lider_area' => $this->faker->boolean,
            'es_director' => $this->faker->boolean(false),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
