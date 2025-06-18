<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Classroom>
 */
class ClassroomFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = $this->faker->randomElement(['Обычный', 'Компьютерный', 'Лаборатория', 'Актовый зал']);
        $computers = $type === 'Компьютерный' ? $this->faker->numberBetween(20, 50) : 0;

        return [
            'number' => $this->faker->unique()->bothify('###'),
            'capacity' => $this->faker->numberBetween(30, 60),
            'type' => $type,
            'computers' => $computers,
        ];
    }
}
