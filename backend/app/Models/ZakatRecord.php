<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ZakatRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'period_year',
        'period_label',
        'amount',
        'payer_name',
        'notes',
        'admin_id',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'period_year' => 'integer',
        ];
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function distributions()
    {
        return $this->hasMany(ZakatDistribution::class);
    }
}
