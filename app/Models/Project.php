<?php

namespace App\Models;

use App\Enums\ProjectRolesEnum;
use App\Enums\ProjectStatusEnum;
use App\Enums\MembersStatusEnum;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = ['name', 'description', 'external_link', 'status'];

    protected $casts = [
        'status' => ProjectStatusEnum::class,
    ];

    /**
     * Assigned members
     */
    public function members()
    {
        return $this->belongsToMany(User::class, 'project_members')
            ->using(ProjectMember::class)
            ->withPivot('role', 'id', 'status', 'approved_at', 'label')
            ->as('membership')
            ->withTimestamps();
    }

    /**
     * empty member slots
     */
    public function slots()
    {
        return $this->hasMany(ProjectMember::class)->whereNull('user_id')->where('status', MembersStatusEnum::EMPTY, true);
    }

    public function owners()
    {
        return $this->members()->wherePivot('role', ProjectRolesEnum::OWNER)->wherePivot('status', MembersStatusEnum::APPROVED, true)->get();
    }

    public function locations()
    {
        return $this->hasMany(Location::class);
    }

    public function schedules()
    {
        return $this->hasMany(ProjectSchedule::class);
    }

    public function approvedMembers()
    {
        return $this->members()->wherePivot('status', MembersStatusEnum::APPROVED->value);
    }
}
