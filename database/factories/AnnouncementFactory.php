<?php

namespace Database\Factories;

use App\Models\Announcement;
use Illuminate\Database\Eloquent\Factories\Factory;

class AnnouncementFactory extends Factory
{
    protected $model = Announcement::class;

    public function definition(): array
    {
        $titles = [
            'Pemberitahuan Libur Nasional',
            'Perubahan Jam Kerja',
            'Kebijakan Baru Absensi',
            'Pelatihan Karyawan',
            'Pengumuman Hasil Evaluasi',
            'Informasi THR',
            'Peraturan Perusahaan Terbaru',
            'Jadwal Meeting Bulanan',
            'Program Kesehatan Karyawan',
            'Peringatan Hari Besar',
            'Pembaharuan Sistem Absensi',
            'Pengumuman Mutasi Karyawan',
            'Informasi BPJS',
            'Pengumuman Libur Bersama',
            'Sosialisasi SOP Baru',
        ];

        return [
            'title' => $this->faker->randomElement($titles),
            'content' => $this->faker->paragraphs(3, true),
            'type' => $this->faker->randomElement(['info', 'warning', 'important', 'urgent']),
            'is_active' => $this->faker->boolean(80),
            'published_at' => $this->faker->dateTimeBetween('-1 month', '+1 month'),
            'created_by' => $this->faker->randomElement(\App\Models\User::pluck('id')->toArray()),
        ];
    }
}
