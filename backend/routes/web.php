<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CandidateController;
use App\Http\Controllers\Admin\IndustryController;
use App\Http\Controllers\Admin\JobController;
use App\Http\Controllers\Admin\JobModerationController;
use App\Http\Controllers\Admin\RecruiterController;
use App\Http\Controllers\Admin\StatisticController;
use App\Http\Controllers\Client\HomeController;
use App\Http\Controllers\Client\AuthController as ClientAuthController;
use App\Http\Controllers\Client\JobController as ClientJobController;
use App\Http\Controllers\Client\RegisteredAccountController;
use App\Http\Controllers\Recruiter\CompanyController as RecruiterCompanyController;
use App\Http\Controllers\Recruiter\ApplicationController as RecruiterApplicationController;
use App\Http\Controllers\Recruiter\JobController as RecruiterJobController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('viec-lam/{slug}', [ClientJobController::class, 'show'])->name('jobs.show');

Route::middleware('guest')->group(function () {
    Route::get('dang-nhap', [ClientAuthController::class, 'create'])->name('login');
    Route::post('dang-nhap', [ClientAuthController::class, 'store'])->middleware('throttle:5,1')->name('login.store');
    Route::get('dang-ky', [RegisteredAccountController::class, 'create'])->name('register');
    Route::post('dang-ky', [RegisteredAccountController::class, 'store'])->middleware('throttle:5,1')->name('register.store');
});

Route::post('dang-xuat', [RegisteredAccountController::class, 'destroy'])->middleware('auth')->name('logout');

Route::prefix('nha-tuyen-dung')->name('recruiter.')->middleware(['auth', 'recruiter'])->group(function () {
    Route::get('thong-tin-cong-ty', [RecruiterCompanyController::class, 'edit'])->name('company.edit');
    Route::put('thong-tin-cong-ty', [RecruiterCompanyController::class, 'update'])->name('company.update');
    Route::get('ung-vien', [RecruiterApplicationController::class, 'index'])->name('applications.index');
    Route::get('ung-vien/{application}', [RecruiterApplicationController::class, 'show'])->name('applications.show');
    Route::patch('ung-vien/{application}/trang-thai', [RecruiterApplicationController::class, 'updateStatus'])->name('applications.status');
    Route::get('ung-vien/{application}/cv/{resume}/xem', [RecruiterApplicationController::class, 'viewResume'])->name('applications.resumes.view');
    Route::get('ung-vien/{application}/cv/{resume}/tai', [RecruiterApplicationController::class, 'downloadResume'])->name('applications.resumes.download');
    Route::get('tin-tuyen-dung', [RecruiterJobController::class, 'index'])->name('jobs.index');
    Route::get('tin-tuyen-dung/tao', [RecruiterJobController::class, 'create'])->name('jobs.create');
    Route::post('tin-tuyen-dung', [RecruiterJobController::class, 'store'])->name('jobs.store');
    Route::get('tin-tuyen-dung/{job}/sua', [RecruiterJobController::class, 'edit'])->name('jobs.edit');
    Route::put('tin-tuyen-dung/{job}', [RecruiterJobController::class, 'update'])->name('jobs.update');
    Route::post('tin-tuyen-dung/{job}/gui-duyet', [RecruiterJobController::class, 'submit'])->name('jobs.submit');
    Route::post('tin-tuyen-dung/{job}/dong', [RecruiterJobController::class, 'close'])->name('jobs.close');
    Route::post('tin-tuyen-dung/{job}/an', [RecruiterJobController::class, 'hide'])->name('jobs.hide');
    Route::delete('tin-tuyen-dung/{job}', [RecruiterJobController::class, 'destroy'])->name('jobs.destroy');
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

        Route::post('jobs/{job}/moderate', [JobModerationController::class, 'moderate'])->name('jobs.moderate');

        Route::resource('industries', IndustryController::class)->except('show');
    });
});
