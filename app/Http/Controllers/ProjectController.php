<?php

namespace App\Http\Controllers;

use App\Enums\ProjectRolesEnum;
use App\Enums\StatusEnum;
use App\Models\Project;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function show(Project $project)
    {
        return inertia('Projects/Show', [
            'project' => $project->load('members'),
        ]);
    }


    public function create()
    {
        return inertia('Projects/Create', [
            'roles' => ProjectRolesEnum::cases(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'join_as' => ['nullable', Rule::enum(ProjectRolesEnum::class)],
        ]);

        $user = $request->user();

        $project = $user->projects()->create($request->only('name', 'description'), [
            'status' => StatusEnum::APPROVED,
            'approved_at' => now(),
            'role' => $request->join_as ?? ProjectRolesEnum::OWNER,
        ]);

        return redirect()->route('projects.show', $project);
    }
}
