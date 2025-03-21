<?php

namespace App\Http\Controllers;

use App\Enums\ProjectRolesEnum;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class ProjectMemberController extends Controller
{
    public function store(Request $request, Project $project)
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

        $project->members()->attach($user->id, [
            'role' => $role,
            'status' => 'pending',
        ]);

        return redirect()->route('project.show', $project);
    }
}
