<?php

use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectMemberController;
use App\Models\Project;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard', [
        'projects' => Project::with('members')->get()
            ->filter(fn($project) => Gate::allows('view', $project))
            ->map(function ($project) {
                $visibleMembers = $project->members
                    ->filter(fn($member) => Gate::allows('view', $member->membership))
                    ->values();

                return $project->setRelation('members', $visibleMembers);
            })
            ->values(),
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__ . '/project.php';

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
