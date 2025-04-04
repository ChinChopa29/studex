<?php

namespace Database\Factories;

use App\Models\EducationProgram;
use Illuminate\Database\Eloquent\Factories\Factory;

class EducationProgramFactory extends Factory
{
    protected $model = EducationProgram::class;

    public function definition()
    {
        return [
            'title' => $this->faker->word,
            'description' => $this->faker->word,
            'duration' => $this->faker->numberBetween(1, 4),
            'degree' => $this->faker->randomElement(['Бакалавриат', 'Магистратура', 'Аспирантура']),
            'price' => $this->faker->numberBetween(50000, 600000),
            'type' => $this->faker->randomElement(['Очная', 'Очно-заочная', 'Дистанционная']),
            'acronym' => $this->faker->word,
        ];
    }
}
