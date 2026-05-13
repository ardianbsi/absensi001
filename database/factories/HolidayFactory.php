<?php

namespace Database\Factories;

use App\Models\Holiday;
use Illuminate\Database\Eloquent\Factories\Factory;

class HolidayFactory extends Factory
{
    protected $model = Holiday::class;

    public function definition(): array
    {
        $holidays = [
            ['name' => 'Tahun Baru Masehi', 'date' => date('Y') . '-01-01', 'is_recurring' => true],
            ['name' => 'Tahun Baru Imlek', 'date' => date('Y') . '-01-22', 'is_recurring' => false],
            ['name' => 'Isra Miraj Nabi Muhammad SAW', 'date' => date('Y') . '-02-18', 'is_recurring' => false],
            ['name' => 'Hari Raya Nyepi', 'date' => date('Y') . '-03-22', 'is_recurring' => false],
            ['name' => 'Wafat Isa Almasih', 'date' => date('Y') . '-04-07', 'is_recurring' => false],
            ['name' => 'Hari Buruh Internasional', 'date' => date('Y') . '-05-01', 'is_recurring' => true],
            ['name' => 'Hari Kebangkitan Nasional', 'date' => date('Y') . '-05-20', 'is_recurring' => true],
            ['name' => 'Hari Lahir Pancasila', 'date' => date('Y') . '-06-01', 'is_recurring' => true],
            ['name' => 'Hari Raya Idul Adha', 'date' => date('Y') . '-06-29', 'is_recurring' => false],
            ['name' => 'Tahun Baru Islam', 'date' => date('Y') . '-07-19', 'is_recurring' => false],
            ['name' => 'Hari Kemerdekaan RI', 'date' => date('Y') . '-08-17', 'is_recurring' => true],
            ['name' => 'Maulid Nabi Muhammad SAW', 'date' => date('Y') . '-09-27', 'is_recurring' => false],
            ['name' => 'Hari Kesaktian Pancasila', 'date' => date('Y') . '-10-01', 'is_recurring' => true],
            ['name' => 'Hari Pahlawan', 'date' => date('Y') . '-11-10', 'is_recurring' => true],
            ['name' => 'Hari Ibu', 'date' => date('Y') . '-12-22', 'is_recurring' => true],
            ['name' => 'Hari Raya Natal', 'date' => date('Y') . '-12-25', 'is_recurring' => true],
        ];

        $holiday = $this->faker->unique()->randomElement($holidays);

        return $holiday + [
            'year' => date('Y'),
            'description' => $this->faker->sentence(),
        ];
    }
}
