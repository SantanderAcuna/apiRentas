<?php

namespace Database\Factories;

use App\Models\Notificacion;
use App\Models\Peticion;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Notificacion>
 */
class NotificacionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Notificacion::class;

    public function definition()
    {
        return [
            'id_funcionario' => User::factory(),
            'id_peticion' => Peticion::factory(),
            'fecha_vencimiento' => Carbon::now()->addDays($this->faker->numberBetween(1, 30)),
            'id_lider_area' => User::factory(),
        ];
    }
}
