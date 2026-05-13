<?php

namespace Database\Factories;

use App\Models\Position;
use Illuminate\Database\Eloquent\Factories\Factory;

class PositionFactory extends Factory
{
    protected $model = Position::class;

    public function definition(): array
    {
        $positions = [
            'Staff', 'Senior Staff', 'Supervisor', 'Assistant Manager',
            'Manager', 'Senior Manager', 'Director', 'Vice President',
            'Head of Department', 'Specialist',
        ];

        return [
            'department_id' => $this->faker->randomElement(\App\Models\Department::pluck('id')->toArray()),
            'code' => 'POS-' . strtoupper($this->faker->unique()->bothify('???###')),
            'name' => $this->faker->randomElement($positions),
            'description' => $this->faker->sentence(),
            'is_active' => true,
        ];
    }
}
