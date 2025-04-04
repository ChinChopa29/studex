<?php

namespace Database\Factories;

use App\Models\Teacher;
use Illuminate\Database\Eloquent\Factories\Factory;

class EducationProgramFactory extends Factory
{
    protected $model = Teacher::class;

    public function definition()
    {
        return [
            'name' => $this->faker->word,
            'surname' => $this->faker->word,
            'lastname' => $this->faker->word,
            'iin' => $this->faker->numberBetween(000000000000, 999999999999),
            'phone' => $this->faker->numberBetween(00000000000, 99999999999),
            'birthday' => $this->faker->date('Y-m-d'),
            'email' => $this->faker->email,
            'password' => $this->faker->password(6, 20),
        ];
    }
}
