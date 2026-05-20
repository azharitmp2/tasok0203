<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Graduation;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Cipta Akaun Admin Terlebih Dahulu
        User::factory()->admin()->create([
            'name' => 'Admin User',
            'email' => 'admin@devhub.test',
        ]);

        // 2. Cipta Sesi Konvokasi (Graduation Session)
        $june = Graduation::factory()->open()->create([
            'title' => 'June 2026 Convocation',
            'ceremony_date' => '2026-06-15',
            'fee' => 250.00,
        ]);

        // 3. Cipta Roster Data Pelajar
        Student::factory()->count(10)->verified()->for($june)->create();
        Student::factory()->count(4)->paidUnverified()->for($june)->create();
        Student::factory()->count(6)->for($june)->create();

        // 4. CRITICAL: Cipta data profil Student demo sebelum akaun User-nya dibina
        Student::factory()->for($june)->create([
            'name' => 'Student User',
            'email' => 'student@devhub.test',
        ]);

        // 5. Cipta Akaun User demo — Fungsi booted() akan auto-link ke data di atas
        User::factory()->create([
            'name' => 'Student User',
            'email' => 'student@devhub.test',
        ]);

        // Bulk data tambahan untuk sesi konvokasi lain
        Graduation::factory()->count(2)
            ->has(Student::factory()->count(15))
            ->create();
    }
}