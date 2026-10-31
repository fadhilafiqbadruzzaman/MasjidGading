<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ZakatDistribution extends Model
{
    use HasFactory;

    protected $fillable = [
        'zakat_record_id',
        'type',
        'recipient_name',
        'recipient_category',
        'amount',
        'distributed_at',
        'notes',
        'admin_id',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'distributed_at' => 'date',
        ];
    }

    public function zakatRecord()
    {
        return $this->belongsTo(ZakatRecord::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
