<?php

use App\Http\Controllers\LocationController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectMemberController;
use App\Http\Controllers\ProjectScheduleController;
use App\Models\Project;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'project', 'middleware' => 'auth'], function () {
    Route::get('/create', [ProjectController::class, 'create'])->name('project.create')->can('create', Project::class);
    Route::post('/', [ProjectController::class, 'store'])->name('project.store');

    /**
     * Project show Tabs
     */
    Route::redirect('/{project}', '/project/{project}/summary');
    Route::get('/{project}/summary', [ProjectController::class, 'show'])->name('project.summary');
    Route::get('/{project}/members', [ProjectMemberController::class, 'index'])->name('project.members');
    Route::get('/{project}/locations', [LocationController::class, 'index'])->name('project.locations');
    Route::get('/{project}/schedule', [ProjectScheduleController::class, 'index'])->name('project.schedule');


    Route::patch('/{project}', [ProjectController::class, 'update'])->name('project.update');

    // members
    Route::post('/{project}/members', [ProjectMemberController::class, 'store'])->name('project.members.store');
    Route::put('/{project}/members/{member}', [ProjectMemberController::class, 'update'])->name('project.members.update');
    Route::delete('/{project}/members/{member}', [ProjectMemberController::class, 'destroy'])->name('project.members.destroy');
    Route::post('/{project}/members/{member}/apply', [ProjectMemberController::class, 'apply'])->name('project.members.apply');
    Route::post('/{project}/members/{member}/checkout', [ProjectMemberController::class, 'checkout'])->name('project.members.checkout');

    // locations
    Route::post('/{project}/locations', [LocationController::class, 'store'])->name('project.locations.store');
    Route::put('/{project}/locations/{location}', [LocationController::class, 'update'])->name('project.locations.update');
    Route::delete('/{project}/locations/{location}', [LocationController::class, 'destroy'])->name('project.locations.destroy');


    // schedules
});

Route::get('/test', function () {
    return 'test';
});
