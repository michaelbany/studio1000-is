<?php

namespace App\Models;

use App\Enums\ProjectRolesEnum;
use App\Enums\MembersStatusEnum;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ProjectMember extends Pivot
{
    protected $fillable = ['role', 'status', 'approved_at', 'label', 'user_id', 'project_id'];
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

    public function approve()
    {
        $slot = $this->project->slots()->where('role', $this->role)->where('label', $this->label, true)->first();
        
        if ($slot) {
            $slot->update([
                'user_id' => $this->user_id,
                'status' => MembersStatusEnum::APPROVED,
                'approved_at' => Carbon::now(),
            ]);

            $otherSlots = $this->project->slots()->where('role', $this->role)->where('label', $this->label, true)->exists();
            if (!$otherSlots) {
                ProjectMember::where('project_id', $this->project_id)
                ->where('role', $this->role)
                ->where(fn($q) => $this->label
                     ? $q->where('label', $this->label, true) 
                     : $q->whereNull('label')
                )
                ->where('status', MembersStatusEnum::PENDING)
                ->where('id', '!=', $this->id)
                ->get()->each->reject();
            }

            $this->delete();
        } else {

            return redirect()->back()->withErrors([
                'checkout' => 'No slots available for this role.',
            ]);
        }
    }

    public function reject(): void
    {
        if ($this->status === MembersStatusEnum::APPROVED) {
            $slot = $this->makeEmpty($this->project);
            $slot->role = $this->role;
            $slot->label = $this->label;
            $slot->save();
        }

        $this->update([
            'status' => MembersStatusEnum::REJECTED,
            'approved_at' => NULL,
        ]);
    }

    public function pending(): void
    {
        if ($this->status === MembersStatusEnum::APPROVED) {
            $slot = $this->makeEmpty($this->project);
            $slot->role = $this->role;
            $slot->label = $this->label;
            $slot->save();
        }

        $this->update([
            'status' => MembersStatusEnum::PENDING,
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

    public static function makeEmpty(Project $project): self
    {
        return self::make([
            'status' => MembersStatusEnum::EMPTY,
            'approved_at' => NULL,
            'user_id' => NULL,
            'project_id' => $project->id,
        ]);
    }


    public function isEmpty(): bool
    {
        return $this->status === MembersStatusEnum::EMPTY && $this->user_id === NULL;
    }
}
