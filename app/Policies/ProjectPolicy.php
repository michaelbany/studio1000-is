<?php

namespace App\Policies;

use App\Enums\ProjectRolesEnum;
use App\Enums\ProjectStatusEnum;
use App\Enums\RolesEnum;
use App\Models\Project;
use App\Models\ProjectMember;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ProjectPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function list(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Project $project): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->role === RolesEnum::MODERATOR || $user->role === RolesEnum::ADMIN;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Project $project): bool
    {
        return $user->role === RolesEnum::ADMIN || $project->owners()->contains($user);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Project $project): bool
    {
        return $user->role === RolesEnum::ADMIN;
    }

    /**
     * User can join the team
     */
    public function join(User $user, Project $project): bool
    {
        return $project->status === ProjectStatusEnum::OPEN || $project->owners()->contains($user) || $user->role === RolesEnum::ADMIN;
    }

    /**
     * User can leave the team
     */
    public function leave(User $user, Project $project): bool
    {
        return ! $project->owners()->contains($user);
    }

    public function member_checkout(User $user, Project $project): bool
    {
        return $user->role === RolesEnum::ADMIN || $project->owners()->contains($user);
    }

    public function member_update(User $user, Project $project): bool
    {
        return $user->role === RolesEnum::ADMIN;
    }

    public function member_delete(User $user, Project $project): bool
    {
        return $user->role === RolesEnum::ADMIN;
    }
}
