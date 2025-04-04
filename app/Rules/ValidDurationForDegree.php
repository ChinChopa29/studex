<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidDurationForDegree implements ValidationRule
{
    protected $degree;

    public function __construct($degree)
    {
        $this->degree = $degree;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $validDurations = [
            'Бакалавриат' => [3, 4],
            'Магистратура' => [1, 2],
            'Аспирантура' => [3, 4, 5],
        ];

        if (!isset($validDurations[$this->degree]) || !in_array((int)$value, $validDurations[$this->degree])) {
            $fail('Недопустимая длительность для выбранной степени.');
        }
    }
}
