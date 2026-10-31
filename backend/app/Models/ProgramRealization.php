<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgramRealization extends Model
{
    use HasFactory;

    protected $fillable = [
        'allocation_id',
        'description',
        'amount_used',
        'realization_date',
        'document_path',
        'notes',
        'admin_id',
    ];

    protected function casts(): array
    {
        return [
            'amount_used' => 'decimal:2',
            'realization_date' => 'date',
        ];
    }

    public function allocation()
    {
        return $this->belongsTo(ProgramAllocation::class, 'allocation_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
