<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Employee;
use App\Models\Position;
use App\Models\Shift;
use App\Models\User;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        $departments = Department::all();
        $shifts = Shift::all();
        $users = User::all();

        $managerUsers = $users->filter(fn ($u) => $u->hasRole('Manager'))->values();
        $hrUsers = $users->filter(fn ($u) => $u->hasRole('HR'))->values();
        $employeeUsers = $users->filter(fn ($u) => $u->hasRole('Employee'))->values();

        $firstNamesMale = ['Agus', 'Bambang', 'Cahyo', 'Dedi', 'Eko', 'Fajar', 'Gunawan', 'Hendra', 'Irfan', 'Joko', 'Kurniawan', 'Lukman', 'Mulyono', 'Nugroho', 'Purnomo', 'Rudi', 'Slamet', 'Totok', 'Wahyudi', 'Yanto', 'Adi', 'Bayu', 'Candra', 'Danang', 'Edi'];
        $firstNamesFemale = ['Ani', 'Bunga', 'Citra', 'Dewi', 'Endang', 'Fitri', 'Gita', 'Hesti', 'Indah', 'Julia', 'Kartika', 'Lestari', 'Maya', 'Nina', 'Putri', 'Ratna', 'Sari', 'Tuti', 'Wulan', 'Yuli', 'Ayu', 'Dian', 'Eka', 'Fany', 'Rina'];
        $lastNames = ['Pratama', 'Wijaya', 'Kusuma', 'Santoso', 'Hidayat', 'Nugraha', 'Saputra', 'Purnama', 'Utama', 'Wibowo', 'Cahyono', 'Susanto', 'Setiawan', 'Hartono', 'Wahyuni', 'Handayani', 'Permadi', 'Kurniawan', 'Syahrir', 'Nasution'];

        // Create Super Admin employee
        $saUser = $users->filter(fn ($u) => $u->hasRole('Super Admin'))->first();
        Employee::create([
            'user_id' => $saUser->id,
            'nik' => 'EMP' . now()->format('Y') . '00001',
            'full_name' => 'Super Admin',
            'email' => $saUser->email,
            'phone' => '081234567890',
            'address' => 'Jl. Sudirman No. 1, Jakarta',
            'birth_date' => '1990-01-01',
            'gender' => 'male',
            'department_id' => $departments->first()->id,
            'position_id' => Position::inRandomOrder()->first()->id,
            'employment_status' => 'permanent',
            'join_date' => '2020-01-01',
            'manager_id' => null,
            'shift_id' => $shifts->first()->id,
            'photo' => null,
            'is_active' => true,
        ]);

        // Create HR employees
        foreach ($hrUsers as $i => $user) {
            $gender = 'female';
            $name = $faker->randomElement($firstNamesFemale) . ' ' . $faker->randomElement($lastNames);
            Employee::create([
                'user_id' => $user->id,
                'nik' => 'EMP' . now()->format('Y') . str_pad(100 + $i + 1, 5, '0', STR_PAD_LEFT),
                'full_name' => $name,
                'email' => $user->email,
                'phone' => '08' . $faker->numerify('##########'),
                'address' => $faker->streetAddress() . ', ' . $faker->city() . ', Indonesia',
                'birth_date' => $faker->dateTimeBetween('-55 years', '-20 years')->format('Y-m-d'),
                'gender' => $gender,
                'department_id' => $departments->where('code', 'HR')->first()->id,
                'position_id' => Position::whereHas('department', fn ($q) => $q->where('code', 'HR'))->inRandomOrder()->first()->id,
                'employment_status' => 'permanent',
                'join_date' => $faker->dateTimeBetween('-5 years', '-1 year')->format('Y-m-d'),
                'manager_id' => Employee::whereHas('user', fn ($q) => $q->whereHas('roles', fn ($r) => $r->where('name', 'Super Admin')))->first()->id,
                'shift_id' => $shifts->first()->id,
                'photo' => null,
                'is_active' => true,
            ]);
        }

        // Create Manager employees
        foreach ($managerUsers as $i => $user) {
            $gender = $i % 2 === 0 ? 'male' : 'female';
            $firstNames = $gender === 'male' ? $firstNamesMale : $firstNamesFemale;
            $name = $faker->randomElement($firstNames) . ' ' . $faker->randomElement($lastNames);
            $dept = $departments->get($i % $departments->count());

            Employee::create([
                'user_id' => $user->id,
                'nik' => 'EMP' . now()->format('Y') . str_pad(200 + $i + 1, 5, '0', STR_PAD_LEFT),
                'full_name' => $name,
                'email' => $user->email,
                'phone' => '08' . $faker->numerify('##########'),
                'address' => $faker->streetAddress() . ', ' . $faker->city() . ', Indonesia',
                'birth_date' => $faker->dateTimeBetween('-50 years', '-25 years')->format('Y-m-d'),
                'gender' => $gender,
                'department_id' => $dept->id,
                'position_id' => Position::whereHas('department', fn ($q) => $q->where('id', $dept->id))->where('name', 'like', '%Manager%')->inRandomOrder()->first()->id ?? Position::inRandomOrder()->first()->id,
                'employment_status' => 'permanent',
                'join_date' => $faker->dateTimeBetween('-10 years', '-2 years')->format('Y-m-d'),
                'manager_id' => Employee::whereHas('user', fn ($q) => $q->whereHas('roles', fn ($r) => $r->where('name', 'Super Admin')))->first()->id,
                'shift_id' => $shifts->first()->id,
                'photo' => null,
                'is_active' => true,
            ]);
        }

        // Create Employee users
        $managerEmployees = Employee::whereHas('user', fn ($q) => $q->whereHas('roles', fn ($r) => $r->where('name', 'Manager')))->get();

        foreach ($employeeUsers as $i => $user) {
            $gender = $faker->randomElement(['male', 'female']);
            $firstNames = $gender === 'male' ? $firstNamesMale : $firstNamesFemale;
            $name = $faker->randomElement($firstNames) . ' ' . $faker->randomElement($lastNames);
            $dept = $departments->get($i % $departments->count());

            Employee::create([
                'user_id' => $user->id,
                'nik' => 'EMP' . now()->format('Y') . str_pad(300 + $i + 1, 5, '0', STR_PAD_LEFT),
                'full_name' => $name,
                'email' => $user->email,
                'phone' => '08' . $faker->numerify('##########'),
                'address' => $faker->streetAddress() . ', ' . $faker->city() . ', Indonesia',
                'birth_date' => $faker->dateTimeBetween('-55 years', '-20 years')->format('Y-m-d'),
                'gender' => $gender,
                'department_id' => $dept->id,
                'position_id' => Position::whereHas('department', fn ($q) => $q->where('id', $dept->id))->inRandomOrder()->first()->id,
                'employment_status' => $faker->randomElement(['permanent', 'contract', 'intern', 'probation']),
                'join_date' => $faker->dateTimeBetween('-2 years', 'now')->format('Y-m-d'),
                'manager_id' => $managerEmployees->random()->id,
                'shift_id' => $shifts->random()->id,
                'photo' => null,
                'is_active' => true,
            ]);
        }
    }
}
