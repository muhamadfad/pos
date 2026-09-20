<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
        'is_decoy',
        'branch_id',
        'can_manage_pusat'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
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
            'is_active' => 'boolean',
            'is_decoy' => 'boolean',
            'can_manage_pusat' => 'boolean',
        ];
    }
    // Tambahkan method ini
    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isAdmin(): bool
    {
        return in_array($this->role, ['admin', 'super_admin']);
    }

    public function isKasir(): bool
    {
        return $this->role === 'kasir';
    }

            public function canManagePusat(): bool
    {
        return $this->isSuperAdmin() || ($this->isAdmin() && $this->can_manage_pusat);
    }
        
        return false;
    }

    public function canAccessBranch($branchId): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }
        return $this->branch_id == $branchId;
    }
}
