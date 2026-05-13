<?php

namespace Database\Factories;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmployeeFactory extends Factory
{
    protected $model = Employee::class;

    public function definition(): array
    {
        $gender = $this->faker->randomElement(['male', 'female']);
        $firstName = $gender === 'male'
            ? $this->faker->randomElement(['Agus', 'Bambang', 'Cahyo', 'Dedi', 'Eko', 'Fajar', 'Gunawan', 'Hendra', 'Irfan', 'Joko', 'Kurniawan', 'Lukman', 'Mulyono', 'Nugroho', 'Purnomo', 'Rudi', 'Slamet', 'Totok', 'Wahyudi', 'Yanto'])
            : $this->faker->randomElement(['Ani', 'Bunga', 'Citra', 'Dewi', 'Endang', 'Fitri', 'Gita', 'Hesti', 'Indah', 'Julia', 'Kartika', 'Lestari', 'Maya', 'Nina', 'Putri', 'Ratna', 'Sari', 'Tuti', 'Wulan', 'Yuli']);
        $lastName = $this->faker->randomElement(['Pratama', 'Wijaya', 'Kusuma', 'Santoso', 'Hidayat', 'Nugraha', 'Saputra', 'Purnama', 'Utama', 'Wibowo', 'Cahyono', 'Susanto', 'Setiawan', 'Hartono', 'Wahyuni']);

        return [
            'nik' => 'EMP' . now()->format('Y') . $this->faker->unique()->numerify('#####'),
            'full_name' => $firstName . ' ' . $lastName,
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => '08' . $this->faker->numerify('##########'),
            'address' => $this->faker->streetAddress() . ', ' . $this->faker->city() . ', Indonesia',
            'birth_date' => $this->faker->dateTimeBetween('-55 years', '-20 years')->format('Y-m-d'),
            'gender' => $gender,
            'department_id' => $this->faker->randomElement(\App\Models\Department::pluck('id')->toArray()),
            'position_id' => $this->faker->randomElement(\App\Models\Position::pluck('id')->toArray()),
            'employment_status' => $this->faker->randomElement(['permanent', 'contract', 'intern', 'probation']),
            'join_date' => $this->faker->dateTimeBetween('-2 years', 'now')->format('Y-m-d'),
            'manager_id' => null,
            'photo' => $this->faker->optional(0.3)->imageUrl(200, 200, 'people'),
            'is_active' => true,
        ];
    }
}
