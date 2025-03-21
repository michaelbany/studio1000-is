<?php

namespace App\Models;

use App\Enums\ProjectRolesEnum;
use App\Enums\ProjectStatusEnum;
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
            ->withPivot('role', 'status', 'approved_at')
            ->as('membership')
            ->withTimestamps();
    }

    public function owners()
    {
        return $this->members()->wherePivot('role', ProjectRolesEnum::OWNER)->get();
    }
}
