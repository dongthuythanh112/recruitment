<?php

use App\Http\Middleware\EnsureUserIsAdmin;
use App\Http\Middleware\EnsureUserIsRecruiter;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use App\Models\User;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => EnsureUserIsAdmin::class,
            'recruiter' => EnsureUserIsRecruiter::class,
        ]);

        $middleware->redirectGuestsTo(fn (Request $request) => $request->is('nha-tuyen-dung', 'nha-tuyen-dung/*')
            ? route('login')
            : route('admin.login'));
        $middleware->redirectUsersTo(fn (Request $request) => match ($request->user()?->role) {
            User::ROLE_ADMIN => route('admin.statistics.index'),
            User::ROLE_RECRUITER => route('recruiter.jobs.index'),
            default => route('home'),
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
