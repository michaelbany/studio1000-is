<?php

namespace App\Models;

use App\Enums\ProjectRolesEnum;
use App\Enums\MembersStatusEnum;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ProjectMember extends Pivot
{
    protected $fillable = ['role', 'status', 'approved_at', 'label'];
    protected $table = 'project_members';

    protected $casts = [
        'approved_at' => 'datetime',
        'role' => ProjectRolesEnum::class,
        'status' => MembersStatusEnum::class,
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approve(): void
    {
        $this->update([
            'status' => MembersStatusEnum::APPROVED,
            'approved_at' => Carbon::now(),
        ]);
    }

    public function reject(): void
    {
        $this->update([
            'status' => MembersStatusEnum::REJECTED,
            'approved_at' => NULL,
        ]);
    }

    public function empty(): void
    {
        $this->update([
            'status' => MembersStatusEnum::EMPTY,
            'approved_at' => NULL,
            'user_id' => NULL,
        ]);
    }

    public function join(User $user, string $role): void
    {
        // 
    }


    public function isEmpty(): bool
    {
        return $this->status === MembersStatusEnum::EMPTY && $this->user_id === NULL;
    }
}