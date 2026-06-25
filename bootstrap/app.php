<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'role'     => \App\Http\Middleware\RoleMiddleware::class,
        'activity' => \App\Http\Middleware\ActivityLogger::class,
    ]);

    $middleware->redirectGuestsTo(function ($request) {
        // Arahkan ke login yang sesuai berdasarkan URL
        if (str_starts_with($request->path(), 'karyawan')) {
            return route('employee.login');
        }
        if (str_starts_with($request->path(), 'admin') ||
            str_starts_with($request->path(), 'bos') ||
            str_starts_with($request->path(), 'it')) {
            return route('staff.login');
        }
        return route('customer.login');
    });
})
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();