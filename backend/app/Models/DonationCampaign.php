<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DonationCampaign extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'target_amount',
        'collected_amount',
        'start_date',
        'end_date',
        'status',
        'image_path',
        'admin_id',
    ];

    protected function casts(): array
    {
        return [
            'target_amount' => 'decimal:2',
            'collected_amount' => 'decimal:2',
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function donations()
    {
        return $this->hasMany(Donation::class, 'campaign_id');
    }

    public function programAllocations()
    {
        return $this->hasMany(ProgramAllocation::class, 'campaign_id');
    }

    public function getProgressPercentageAttribute(): float
    {
        if (!$this->target_amount || $this->target_amount <= 0) {
            return 0;
        }
        $percentage = ((float)$this->collected_amount / (float)$this->target_amount) * 100;
        return round(min($percentage, 100), 1);
    }
}
