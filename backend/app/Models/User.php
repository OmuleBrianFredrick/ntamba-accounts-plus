<?php

namespace App\Models;

use App\Enums\Role;
use App\Models\Concerns\BelongsToCompany;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, BelongsToCompany;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'company_id',
        'name',
        'role',
        'email',
        'phone',
        'agent_code',
        'password',
        'pin_hash',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'pin_hash',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'pin_locked_at' => 'datetime',
            'is_active' => 'boolean',
            'role' => Role::class,
        ];
    }

    public function isAgent(): bool
    {
        return $this->role === Role::Agent;
    }

    /** Agents are locked out after five wrong PIN tries (plan: Sign-in). */
    public const MAX_PIN_ATTEMPTS = 5;

    public function isPinLocked(): bool
    {
        return $this->pin_failed_attempts >= self::MAX_PIN_ATTEMPTS;
    }

    public function verifyPin(string $pin): bool
    {
        if ($this->isPinLocked() || !$this->is_active || !$this->pin_hash) {
            return false;
        }

        if (!Hash::check($pin, $this->pin_hash)) {
            $this->increment('pin_failed_attempts');
            if ($this->pin_failed_attempts >= self::MAX_PIN_ATTEMPTS) {
                $this->update(['pin_locked_at' => now()]);
            }
            return false;
        }

        $this->update(['pin_failed_attempts' => 0, 'pin_locked_at' => null]);
        return true;
    }
}

