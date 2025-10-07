<?php

use App\Http\Middleware\HasStore;
use App\Http\Middleware\IsProvider;
use App\Http\Middleware\IsUser;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'isProvider' => IsProvider::class,
            'hasStore'   => HasStore::class,
            'isUser'   => IsUser::class,
        ]);   
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
