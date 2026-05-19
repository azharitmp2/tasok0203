<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('graduations', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('title');
            $table->date('ceremony_date');
            $table->decimal('fee', 8, 2);
            $table->enum('status', ['draft', 'open', 'closed'])->default('draft');
            $table->timestamps();
            $table->softDeletes(); // Keeps records available for financial auditing
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('graduations');
    }
};
