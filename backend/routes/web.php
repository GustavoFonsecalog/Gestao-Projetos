<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::resource('projects', \App\Http\Controllers\ProjectController::class);
    Route::get('/projects/create', function () {
        return Inertia::render('Projects/Create');
    })->name('projects.create');
    Route::get('/projects/{project}/edit', function (Project $project) {
        $project->load('users');
        return Inertia::render('Projects/Edit', ['project' => $project]);
    })->name('projects.edit');
    Route::get('/projects/{project}', function (Project $project) {
        $project->load('users');
        return Inertia::render('Projects/Show', ['project' => $project]);
    })->name('projects.show');
    Route::post('/projects/{project}/archive', [\App\Http\Controllers\ProjectController::class, 'archive'])->name('projects.archive');
    Route::post('/projects/{project}/duplicate', [\App\Http\Controllers\ProjectController::class, 'duplicate'])->name('projects.duplicate');
    Route::get('/projects/{project}/pdf', [\App\Http\Controllers\ProjectController::class, 'downloadPdf'])->name('projects.pdf');
    Route::post('/projects/{project}/send-email', [\App\Http\Controllers\ProjectController::class, 'sendEmail'])->name('projects.sendEmail');
    Route::get('/projects/{project}/suggestions', [\App\Http\Controllers\ProjectController::class, 'suggestions'])->name('projects.suggestions');
    Route::post('/projects/{project}/suggestions', [\App\Http\Controllers\ProjectController::class, 'addSuggestion'])->name('projects.addSuggestion');
    Route::get('/users', function () {
        return User::select('id', 'name')->get();
    });
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
