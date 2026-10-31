<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QurbanDistribution extends Model
{
    use HasFactory;

    protected $fillable = [
        'program_id',
        'recipient_name',
        'recipient_address',
        'package_count',
        'amount_kg',
        'notes',
        'distributed_at',
    ];

    protected function casts(): array
    {
        return [
            'package_count' => 'integer',
            'amount_kg' => 'decimal:2',
            'distributed_at' => 'date',
        ];
    }

    public function program()
    {
        return $this->belongsTo(QurbanProgram::class, 'program_id');
    }
}
