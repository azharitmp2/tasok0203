<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Graduation;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed global login roles
        User::factory()->admin()->create([
            'name' => 'Admin User',
            'email' => 'admin@devhub.test',
        ]);

        User::factory()->create([
            'name' => 'Student User',
            'email' => 'student@devhub.test',
        ]);

        // 2. Build explicit convocation event
        $june = Graduation::factory()->open()->create([
            'title' => 'June 2026 Convocation',
            'ceremony_date' => '2026-06-15',
            'fee' => 250.00,
        ]);

        // Distribute student metrics inside the event
        Student::factory()->count(10)->verified()->for($june)->create();
        Student::factory()->count(4)->paidUnverified()->for($june)->create();
        Student::factory()->count(6)->for($june)->create();

        // 3. Batch seed fallback background context events
        Graduation::factory()->count(2)
            ->has(Student::factory()->count(15))
            ->create();
    }
}