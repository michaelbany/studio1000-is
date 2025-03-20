<?php

namespace App\Models;

use App\Enums\ProjectRolesEnum;
use App\Enums\StatusEnum;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ProjectMember extends Pivot
{
    protected $fillable = ['role', 'status', 'approved_at'];

    protected $casts = [
        'approved_at' => 'datetime',
        'role' => ProjectRolesEnum::class,
        'status' => StatusEnum::class,
    ];

    public function approve(): void
    {
        $this->update([
            'status' => StatusEnum::APPROVED,
            'approved_at' => Carbon::now(),
        ]);
    }

    public function reject(): void
    {
        $this->update([
            'status' => StatusEnum::REJECTED,
            'approved_at' => NULL,
        ]);
    }
}