<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectSchedule extends Model
{
    protected $fillable = ['location_id', 'project_id', 'title', 'description', 'date'];

    protected $casts = [
        'date' => 'datetime',
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
        return $this->belongsToMany(ProjectMember::class, 'schedule_participants')
            ->using(ScheduleParticipant::class)
            ->withPivot('call_time', 'wrap_time')
            ->as('participant')
            ->withTimestamps();
    }
}
