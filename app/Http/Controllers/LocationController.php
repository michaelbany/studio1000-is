<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class LocationController extends Controller
{
    public function index(Project $project)
    {
        if (Gate::denies('view', $project)) {
            abort(404);
        }

        return inertia('Projects/Locations', [
            'project' => $project,
            'locations' => $project->locations,
        ]);
    }

    public function store(Request $request, Project $project)
    {
        Gate::authorize('update', $project);

        $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'address' => ['required', 'string'],
            'description' => ['nullable', 'string'],
        ]);

        $project->locations()->create($request->only('name', 'address', 'description'));

        return redirect()->route('project.locations', $project);
    }

    public function update(Request $request, Project $project, Location $location)
    {
        Gate::authorize('update', $project);

        $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'address' => ['required', 'string'],
            'description' => ['required', 'string'],
        ]);

        $location->update($request->only('name', 'address', 'description'));

        return redirect()->route('project.locations', $project);
    }

    public function destroy(Project $project, Location $location)
    {
        Gate::authorize('update', $project);
       
        $location->delete();

        return redirect()->route('project.locations', $project);
    }
}
