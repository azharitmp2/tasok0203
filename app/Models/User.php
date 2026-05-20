<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Student;

class User extends Authenticatable
{
    use HasFactory;
    use HasUuids;
    use Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'is_admin',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    public function uniqueIds(): array
    {
        return ['uuid'];
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::created(function (User $user): void {
            // Akaun Admin tidak dihubungkan dengan mana-mana profil Student
            if ($user->is_admin) {
                return;
            }

            // Jalankan satu UPDATE SQL secara senyap untuk hubungkan akaun
            Student::query()
                ->where('email', $user->email)
                ->whereNull('user_id')
                ->update(['user_id' => $user->id]);
        });
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->is_admin === true;
    }

    public function student(): HasOne
    {
        return $this->hasOne(Student::class);
    }
}