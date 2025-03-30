<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectSchedule extends Model
{
    protected $fillable = ['location_id', 'project_id', 'title', 'description', 'start_date', 'end_date', 'color'];
    protected $table = 'project_schedules';
    
    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function participants()
    {
        return $this->belongsToMany(User::class, 'schedule_participants', 'project_schedule_id', 'user_id')
            ->using(ScheduleParticipant::class)
            ->withPivot('call_time', 'wrap_time')
            ->as('participant')
            ->withTimestamps();
    }
}
