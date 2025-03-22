<?php

use App\Http\Controllers\LocationController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectMemberController;
use App\Http\Controllers\ProjectScheduleController;
use App\Models\Project;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'project'], function () {
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

});

Route::get('/test', function () {
    return 'test';
});
