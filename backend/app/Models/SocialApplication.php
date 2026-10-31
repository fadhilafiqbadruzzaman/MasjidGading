<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SocialApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'program_id',
        'user_id',
        'applicant_name',
        'phone',
        'address',
        'reason',
        'requested_amount',
        'status',
        'reviewed_by',
        'reviewed_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'requested_amount' => 'decimal:2',
            'reviewed_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function program()
    {
        return $this->belongsTo(SocialProgram::class, 'program_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function distributions()
    {
        return $this->hasMany(SocialDistribution::class, 'application_id');
    }
}
