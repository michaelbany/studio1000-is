<?php

namespace App\Http\Controllers;

use App\Enums\ScheduleColorEnum;
use App\Models\Project;
use App\Models\ProjectSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class ProjectScheduleController extends Controller
{
    public function index(Project $project)
    {
        if (Gate::denies('view', $project)) {
            abort(404);
        }

        return inertia('Projects/Schedule', [
            'project' => $project,
            'schedules' => $project->schedules->load('participants.user'),
            'locations' => $project->locations,
            'members' => $project->approvedMembers->unique('id'),
            'enum' => [
                'schedule_color' => ScheduleColorEnum::cases(),
            ]
        ]);
    }

    public function store(Request $request, Project $project)
    {
        Gate::authorize('update', $project);

        $request->validate([
            'title' => ['required', 'string', 'max:50'],
            'description' => ['nullable', 'string'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date'],
            'location_id' => ['nullable', 'exists:locations,id'],
            'color' => ['nullable', 'string', Rule::enum(ScheduleColorEnum::class)],
        ]);

        $project->schedules()->create($request->only('title', 'description', 'start_date', 'end_date', 'location_id', 'color'));

        return redirect()->route('project.schedule', $project);
    }

    public function update(Request $request, Project $project, ProjectSchedule $schedule)
    {
        Gate::authorize('update', $project);

        $request->validate([
            'title' => ['required', 'string', 'max:50'],
            'description' => ['nullable', 'string'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date'],
            'location_id' => ['nullable', 'exists:locations,id'],
            'color' => ['nullable', 'string', Rule::enum(ScheduleColorEnum::class)],
            'participants' => ['nullable', 'array'],
        ]);

        $schedule->update($request->only('title', 'description', 'start_date', 'end_date', 'location_id', 'color'));

        if ($request->has('participants')) {
            $schedule->participants()->sync($request->participants);
        } else {
            $schedule->participants()->detach();
        }
        
        return redirect()->route('project.schedule', $project);
    }

    public function destroy(Project $project, ProjectSchedule $schedule)
    {
        Gate::authorize('update', $project);

        $schedule->delete();

        return redirect()->route('project.schedule', $project);
    }
}
