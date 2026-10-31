<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SocialDistribution extends Model
{
    use HasFactory;

    protected $fillable = [
        'program_id',
        'application_id',
        'recipient_name',
        'amount',
        'description',
        'distributed_at',
        'document_path',
        'admin_id',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'distributed_at' => 'date',
        ];
    }

    public function program()
    {
        return $this->belongsTo(SocialProgram::class, 'program_id');
    }

    public function application()
    {
        return $this->belongsTo(SocialApplication::class, 'application_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
