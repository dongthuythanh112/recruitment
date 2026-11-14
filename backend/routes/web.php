<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CandidateController;
use App\Http\Controllers\Admin\IndustryController;
use App\Http\Controllers\Admin\JobController;
use App\Http\Controllers\Admin\JobModerationController;
use App\Http\Controllers\Admin\RecruiterController;
use App\Http\Controllers\Admin\StatisticController;
use App\Http\Controllers\Client\HomeController;
use App\Http\Controllers\Client\JobController as ClientJobController;
use App\Http\Controllers\Client\RegisteredAccountController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('viec-lam/{slug}', [ClientJobController::class, 'show'])->name('jobs.show');

Route::middleware('guest')->group(function () {
    Route::get('dang-ky', [RegisteredAccountController::class, 'create'])->name('register');
    Route::post('dang-ky', [RegisteredAccountController::class, 'store'])->middleware('throttle:5,1')->name('register.store');
});

Route::post('dang-xuat', [RegisteredAccountController::class, 'destroy'])->middleware('auth')->name('logout');

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

        Route::post('jobs/{job}/moderate', [JobModerationController::class, 'moderate'])->name('jobs.moderate');

        Route::resource('industries', IndustryController::class)->except('show');
    });
});
