<?php

namespace App\Http\Controllers;

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
}
