<?php

namespace App\Policies;

use App\Enums\RolesEnum;
use App\Models\ProjectSchedule;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ProjectSchedulePolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ProjectSchedule $projectSchedule): bool
    {
        if ($user->role === RolesEnum::ADMIN) {
            return true;
        }

        if ($projectSchedule->project->owners()->contains($user)) {
            return true;
        }

        if ($projectSchedule->participants->contains($user)) {
            return true;
        }

        return false;
    }
}
