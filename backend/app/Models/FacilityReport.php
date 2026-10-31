<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacilityReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'reported_by',
        'title',
        'description',
        'location',
        'condition',
        'image_path',
        'status',
        'acknowledged_by',
        'resolved_by',
        'notes',
    ];

    public function marbot()
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    public function task()
    {
        return $this->belongsTo(MaintenanceTask::class, 'task_id');
    }

    public function acknowledgedBy()
    {
        return $this->belongsTo(User::class, 'acknowledged_by');
    }

    public function resolvedBy()
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }
}
