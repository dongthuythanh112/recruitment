<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CandidateController;
use App\Http\Controllers\Admin\IndustryController;
use App\Http\Controllers\Admin\JobController;
use App\Http\Controllers\Admin\JobModerationController;
use App\Http\Controllers\Admin\RecruiterController;
use App\Http\Controllers\Admin\StatisticController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('login', [AuthController::class, 'showLogin'])->name('login');
        Route::post('login', [AuthController::class, 'login'])->middleware('throttle:5,1')->name('login.store');
    });

    Route::middleware(['auth', 'admin'])->group(function () {
        Route::post('logout', [AuthController::class, 'logout'])->name('logout');

        Route::redirect('/', '/admin/statistics');
        Route::get('statistics', [StatisticController::class, 'index'])->name('statistics.index');

        Route::patch('candidates/{candidate}/toggle-status', [CandidateController::class, 'toggleStatus'])->name('candidates.toggle-status');
        Route::resource('candidates', CandidateController::class);

        Route::patch('recruiters/{recruiter}/toggle-status', [RecruiterController::class, 'toggleStatus'])->name('recruiters.toggle-status');
        Route::resource('recruiters', RecruiterController::class);

        Route::resource('jobs', JobController::class);

        Route::get('job-moderation', [JobModerationController::class, 'index'])->name('job-moderation.index');
        Route::post('jobs/{job}/moderate', [JobModerationController::class, 'moderate'])->name('jobs.moderate');

        Route::resource('industries', IndustryController::class)->except('show');
    });
});
