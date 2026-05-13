<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $password = Hash::make('password');

        // Super Admin
        $superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@absen.app',
            'password' => $password,
        ]);
        $superAdmin->assignRole('Super Admin');

        // HR
        for ($i = 1; $i <= 2; $i++) {
            $user = User::create([
                'name' => 'HR ' . $i,
                'email' => 'hr' . $i . '@absen.app',
                'password' => $password,
            ]);
            $user->assignRole('HR');
        }

        // Managers
        for ($i = 1; $i <= 5; $i++) {
            $user = User::create([
                'name' => 'Manager ' . $i,
                'email' => 'manager' . $i . '@absen.app',
                'password' => $password,
            ]);
            $user->assignRole('Manager');
        }

        // Employees
        for ($i = 1; $i <= 50; $i++) {
            $user = User::create([
                'name' => 'Employee ' . $i,
                'email' => 'employee' . $i . '@absen.app',
                'password' => $password,
            ]);
            $user->assignRole('Employee');
        }
    }
}
