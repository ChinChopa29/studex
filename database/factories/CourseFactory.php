<?php

namespace Database\Factories;

use App\Models\Course;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factories\Factory;

class CourseFactory extends Factory
{
    protected $model = Course::class;

    public function definition()
    {
        return [
            'name' => $this->faker->word,
            'description' => $this->faker->word,
            'credits' => $this->faker->numberBetween(1, 6),
            'semester' => $this->faker->numberBetween(1, 8),
            'code' => $this->faker->unique()->word,
            'degree' => $this->faker->randomElement(['Бакалавриат', 'Магистратура', 'Аспирантура']),
            'type' => $this->faker->randomElement(['Обязательный', 'Элективный']),
            'education_program_id' => \App\Models\EducationProgram::factory(),
        ];
    }
}

