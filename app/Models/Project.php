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

    public function members()
    {
        return $this->belongsToMany(User::class, 'project_members')
            ->using(ProjectMember::class)
            ->withPivot('role', 'id', 'status', 'approved_at', 'label')
            ->as('membership')
            ->withTimestamps();
    }

    public function owners()
    {
        return $this->members()->wherePivot('role', ProjectRolesEnum::OWNER)->wherePivot('status', MembersStatusEnum::APPROVED, true)->get();
    }
}
