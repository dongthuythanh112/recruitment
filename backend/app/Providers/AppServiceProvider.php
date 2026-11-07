<?php

namespace App\Providers;

use App\Models\Job;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Số tin chờ duyệt hiển thị trên menu quản trị.
        View::composer('admin.layouts.app', function ($view) {
            $view->with('pendingJobsCount', Job::where('status', Job::STATUS_PENDING)->count());
        });
    }
}
