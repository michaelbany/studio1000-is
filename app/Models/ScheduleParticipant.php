<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ScheduleParticipant extends Pivot
{
    protected $fillable = ['call_time', 'wrap_time'];
    protected $table = 'schedule_participants';

    protected $casts = [
        'call_time' => 'time',
        'wrap_time' => 'time',
    ];

    public function schedule()
    {
        return $this->belongsTo(ProjectSchedule::class);
    }

    public function member()
    {
        return $this->belongsTo(ProjectMember::class);
    }
}
