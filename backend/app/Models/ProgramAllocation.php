<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgramAllocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'total_allocated',
        'campaign_id',
        'status',
        'is_published',
        'published_by',
        'published_at',
        'admin_id',
    ];

    protected function casts(): array
    {
        return [
            'total_allocated' => 'decimal:2',
            'is_published' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    public function campaign()
    {
        return $this->belongsTo(DonationCampaign::class, 'campaign_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function publisher()
    {
        return $this->belongsTo(User::class, 'published_by');
    }

    public function realizations()
    {
        return $this->hasMany(ProgramRealization::class, 'allocation_id');
    }

    public function getTotalRealizedAttribute(): float
    {
        return (float)$this->realizations()->sum('amount_used');
    }

    public function getRemainingBalanceAttribute(): float
    {
        return (float)$this->total_allocated - $this->total_realized;
    }
}
