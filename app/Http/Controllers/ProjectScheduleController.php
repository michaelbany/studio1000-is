<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ProjectScheduleController extends Controller
{
    public function index(Project $project)
    {
        if (Gate::denies('view', $project)) {
            abort(404);
        }

        return inertia('Projects/Schedule', [
            'project' => $project,
            'schedule' => $project->schedules,
        ]);
    }
}
