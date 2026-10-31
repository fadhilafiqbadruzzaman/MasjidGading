<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SocialProgram extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'budget',
        'status',
        'is_published',
        'admin_id',
    ];

    protected function casts(): array
    {
        return [
            'budget' => 'decimal:2',
            'is_published' => 'boolean',
        ];
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function applications()
    {
        return $this->hasMany(SocialApplication::class, 'program_id');
    }

    public function distributions()
    {
        return $this->hasMany(SocialDistribution::class, 'program_id');
    }
}
