<?php

namespace App\Http\Controllers;

use App\Enums\ProjectRolesEnum;
use App\Enums\RolesEnum;
use App\Enums\StatusEnum;
use App\Models\Project;
use App\Models\ProjectMember;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class ProjectMemberController extends Controller
{
    public function join(Request $request, Project $project)
    {
        $request->validate([
            'role' => ['required', Rule::enum(ProjectRolesEnum::class)],
        ]);

        $user = $request->user();
        $role = $request->input('role');

        $roleExists = $project->members()->where('user_id', $user->id)->wherePivot('role', $role)->exists();

        if ($roleExists) {
            return redirect()->route('project.show', $project)->withErrors([
                'role' => 'You already have this role in the project.',
            ]);
        }

        $pendingRequests = $project->members()->where('user_id', $user->id)->wherePivot('status', 'pending')->count();

        if ($pendingRequests >= 3) {
            return redirect()->route('project.show', $project)->withErrors([
                'role' => 'You already have 3 or more pending requests.',
            ]);
        }

        $status = $user->role === RolesEnum::ADMIN || $project->owners()->contains($user) ? StatusEnum::APPROVED : StatusEnum::PENDING;

        $project->members()->attach($user->id, [
            'role' => $role,
            'status' => $status,
        ]);

        return redirect()->route('project.show', $project);
    }


    public function checkout(Request $request, Project $project, ProjectMember $member)
    {
        Gate::authorize('member_checkout', $project);

        $request->validate([
            'status' => ['required', Rule::enum(StatusEnum::class)],
        ]);

        if ($request->input('status') === StatusEnum::APPROVED->value) {
            $member->approve();
        } elseif ($request->input('status') === StatusEnum::REJECTED->value) {
            $member->reject();
        } else {
            $member->update([
                'status' => StatusEnum::PENDING,
                'approved_at' => NULL,
            ]);
        }

        return redirect()->route('project.show', $project);
    }
}
