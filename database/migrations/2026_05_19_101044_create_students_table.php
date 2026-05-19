<?php

declare(strict_types=1);

use App\Models\Graduation;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            // Use modern foreignIdFor helper to prevent namespace shifting bugs
            $table->foreignIdFor(Graduation::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(User::class)->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('ic')->unique();
            $table->string('email')->unique();
            $table->string('matric_card');
            $table->string('phone');
            $table->string('payment_receipt')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};