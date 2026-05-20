<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable; // ← New Import

class Student extends Model
{
    use HasFactory;
    use HasUuids;
    use Notifiable;

    protected $fillable = [
        'graduation_id', 'user_id',
        'name', 'ic', 'email', 'matric_card', 'phone',
        'payment_receipt', 'verified_at', 'paid_at',
    ];

    public function uniqueIds(): array
    {
        return ['uuid'];
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    protected function casts(): array
    {
        return [
            'verified_at' => 'datetime',
            'paid_at' => 'datetime',
        ];
    }

    public function graduation(): BelongsTo
    {
        return $this->belongsTo(Graduation::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isVerified(): bool
    {
        return $this->verified_at !== null;
    }

    public function hasPaid(): bool
    {
        return $this->payment_receipt !== null && $this->paid_at !== null;
    }
}