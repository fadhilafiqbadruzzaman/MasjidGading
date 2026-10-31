<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QurbanParticipant extends Model
{
    use HasFactory;

    protected $fillable = [
        'program_id',
        'participant_name',
        'phone',
        'animal_type',
        'shares',
        'amount_paid',
        'payment_status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'shares' => 'integer',
            'amount_paid' => 'decimal:2',
        ];
    }

    public function program()
    {
        return $this->belongsTo(QurbanProgram::class, 'program_id');
    }
}
