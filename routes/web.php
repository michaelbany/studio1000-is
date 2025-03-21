<?php

use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectMemberController;
use App\Models\Project;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard', [
        'projects' => Project::all()->load('members'),
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');


Route::group(['prefix' => 'project'], function () {
    Route::get('/create', [ProjectController::class, 'create'])->name('project.create');
    Route::post('/', [ProjectController::class, 'store'])->name('project.store');
    Route::get('/{project}', [ProjectController::class, 'show'])->name('project.show');
    Route::get('/{project}/edit', [ProjectController::class, 'edit'])->name('project.edit');
    Route::put('/{project}', [ProjectController::class, 'update'])->name('project.update');
    Route::delete('/{project}', [ProjectController::class, 'destroy'])->name('project.destroy');

    Route::group(['prefix' => '{project}/member'], function () {
        Route::post('/', [ProjectMemberController::class, 'store'])->name('project.member.store');
        Route::put('/{member}', [ProjectMemberController::class, 'update'])->name('project.member.update');
        Route::delete('/{member}', [ProjectMemberController::class, 'destroy'])->name('project.member.destroy');
    });

});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
