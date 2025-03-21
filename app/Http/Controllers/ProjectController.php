<?php

namespace App\Http\Controllers;

use App\Enums\ProjectRolesEnum;
use App\Enums\ProjectStatusEnum;
use App\Enums\StatusEnum;
use App\Models\Project;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ProjectController extends Controller
{
    public function show(Project $project)
    {
        if (Gate::denies('view', $project)) {
            abort(404);
        }

        return inertia('Projects/Show', [
            'project' => $project,
            'members' => $project->members
                ->filter(fn($member) => Gate::allows('view', $member->membership))
                ->sortBy(fn($member) => $member->membership->role)
                ->values(),
            'roles' => ProjectRolesEnum::cases(),
            'process' => StatusEnum::cases(),
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
        Gate::authorize('create', Project::class);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'join_as' => ['nullable', Rule::enum(ProjectRolesEnum::class)],
        ]);

        $user = $request->user();

        $project = $user->projects()->create($request->only('name', 'description'), [
            'status' => StatusEnum::APPROVED,
            'approved_at' => now(),
            'role' => ProjectRolesEnum::OWNER,
        ]);

        if ($request->filled('join_as') && $request->input('join_as') !== ProjectRolesEnum::OWNER->value) {
            $project->members()->attach($user->id, [
                'role' => $request->join_as,
                'status' => StatusEnum::APPROVED,
                'approved_at' => now(),
            ]);
        }

        return redirect()->route('project.show', $project);
    }

    public function edit(Project $project)
    {
        return inertia('Projects/Edit', [
            'project' => $project->load('members'),
            'status' => ProjectStatusEnum::cases(),
        ]);
    }

    public function update(Request $request, Project $project)
    {
        Gate::authorize('update', $project);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'external_link' => ['nullable', 'url'],
            'status' => ['required', Rule::enum(ProjectStatusEnum::class)],
        ]);

        $project->update($request->only('name', 'description', 'external_link', 'status'));

        return redirect()->route('project.show', $project);
    }
}
