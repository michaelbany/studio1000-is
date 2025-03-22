<?php

namespace App\Policies;

use App\Enums\RolesEnum;
use App\Enums\MembersStatusEnum;
use App\Models\ProjectMember;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ProjectMemberPolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ProjectMember $projectMember): bool
    {
        if ($user->role === RolesEnum::ADMIN || $projectMember->project->owners()->contains($user)) {
            return true;
        }

        if ($projectMember->user_id === $user->id) {
            return true;
        }

        if ($projectMember->status === MembersStatusEnum::APPROVED) {
            return true;
        }

        return false;
    }
}
