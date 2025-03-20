<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = ['name', 'description', 'external_link'];

    public function members()
    {
        return $this->belongsToMany(User::class, 'project_members')
            ->using(ProjectMember::class)
            ->withPivot('role', 'status', 'approved_at')
            ->as('membership')
            ->withTimestamps();
    }
}
