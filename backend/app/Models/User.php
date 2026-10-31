<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'address',
        'avatar',
        'role',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return in_array($this->role, ['super_admin', 'admin_masjid', 'marbot']) && $this->is_active;
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isAdmin(): bool
    {
        return in_array($this->role, ['super_admin', 'admin_masjid']);
    }

    public function isMarbot(): bool
    {
        return $this->role === 'marbot';
    }

    public function isJamaah(): bool
    {
        return $this->role === 'jamaah';
    }

    // Relationships
    public function eventRegistrations()
    {
        return $this->hasMany(EventRegistration::class);
    }

    public function donations()
    {
        return $this->hasMany(Donation::class);
    }

    public function assignedTasks()
    {
        return $this->hasMany(MaintenanceTask::class, 'assigned_to');
    }

    public function facilityReports()
    {
        return $this->hasMany(FacilityReport::class, 'reported_by');
    }
}
