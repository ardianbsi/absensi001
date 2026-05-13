<?php

namespace Database\Factories;

use App\Models\LeaveType;
use Illuminate\Database\Eloquent\Factories\Factory;

class LeaveTypeFactory extends Factory
{
    protected $model = LeaveType::class;

    public function definition(): array
    {
        $types = [
            ['name' => 'Cuti Tahunan', 'code' => 'ANNUAL', 'quota' => 12, 'is_paid' => true],
            ['name' => 'Sakit', 'code' => 'SICK', 'quota' => 0, 'is_paid' => true],
            ['name' => 'Izin Pribadi', 'code' => 'PERSONAL', 'quota' => 0, 'is_paid' => false],
            ['name' => 'WFH', 'code' => 'WFH', 'quota' => 0, 'is_paid' => true],
            ['name' => 'Dinas Luar', 'code' => 'DUTY', 'quota' => 0, 'is_paid' => true],
            ['name' => 'Cuti Melahirkan', 'code' => 'MATERNITY', 'quota' => 90, 'is_paid' => true],
            ['name' => 'Cuti Menikah', 'code' => 'MARRIAGE', 'quota' => 3, 'is_paid' => true],
            ['name' => 'Ciuman', 'code' => 'KISS', 'quota' => 0, 'is_paid' => false],
        ];

        $type = $this->faker->unique()->randomElement($types);

        return $type + [
            'description' => $this->faker->sentence(),
            'is_active' => true,
        ];
    }
}
