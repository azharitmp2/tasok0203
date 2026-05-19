<?php

namespace Database\Factories;

use App\Models\Student;
use App\Models\Graduation;
use Illuminate\Database\Eloquent\Factories\Factory;

class StudentFactory extends Factory
{
    protected $model = Student::class;

    public function definition(): array
    {
        return [
            'graduation_id' => Graduation::factory(),
            'name' => fake('ms_MY')->name(),
            'ic' => fake()->unique()->numerify('############'),
            'email' => fake()->unique()->safeEmail(),
            'matric_card' => 'M' . fake()->unique()->numerify('######'),
            'phone' => fake('ms_MY')->phoneNumber(),
        ];
    }

    public function verified(): static
    {
        return $this->state(fn () => [
            'payment_receipt' => 'receipts/sample-receipt.pdf',
            'paid_at' => now()->subDays(fake()->numberBetween(1, 14)),
            'verified_at' => now()->subDays(fake()->numberBetween(0, 7)),
        ]);
    }

    public function paidUnverified(): static
    {
        return $this->state(fn () => [
            'payment_receipt' => 'receipts/sample-receipt.pdf',
            'paid_at' => now()->subDays(fake()->numberBetween(1, 3)),
            'verified_at' => null,
        ]);
    }
}