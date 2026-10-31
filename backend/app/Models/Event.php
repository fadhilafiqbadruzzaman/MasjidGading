<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'location',
        'event_date',
        'event_time',
        'end_date',
        'end_time',
        'organizer',
        'capacity',
        'is_registration_open',
        'status',
        'admin_id',
    ];

    protected function casts(): array
    {
        return [
            'event_date' => 'date',
            'end_date' => 'date',
            'capacity' => 'integer',
            'is_registration_open' => 'boolean',
        ];
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function registrations()
    {
        return $this->hasMany(EventRegistration::class);
    }

    public function registeredUsers()
    {
        return $this->belongsToMany(User::class, 'event_registrations')
            ->wherePivot('status', 'registered')
            ->withTimestamps();
    }

    public function getActiveRegistrationsCountAttribute(): int
    {
        return $this->registrations()->where('status', 'registered')->count();
    }

    public function isFull(): bool
    {
        if ($this->capacity === null || $this->capacity <= 0) {
            return false;
        }
        return $this->active_registrations_count >= $this->capacity;
    }
}
