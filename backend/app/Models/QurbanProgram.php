<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QurbanProgram extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'year',
        'description',
        'status',
        'is_published',
        'admin_id',
    ];

    protected function casts(): array
    {
        return [
            'year' => 'integer',
            'is_published' => 'boolean',
        ];
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function participants()
    {
        return $this->hasMany(QurbanParticipant::class, 'program_id');
    }

    public function distributions()
    {
        return $this->hasMany(QurbanDistribution::class, 'program_id');
    }
}
