<?php

namespace Database\Factories;

use App\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;

class DepartmentFactory extends Factory
{
    protected $model = Department::class;

    public function definition(): array
    {
        $departments = [
            'IT', 'HR', 'Finance', 'Marketing', 'Operations',
            'Sales', 'Legal', 'R&D', 'Procurement', 'GA',
        ];

        $name = $this->faker->unique()->randomElement($departments);

        return [
            'code' => strtoupper(substr($name, 0, 3)) . rand(10, 99),
            'name' => $name,
            'description' => $this->faker->sentence(),
            'is_active' => true,
        ];
    }
}
