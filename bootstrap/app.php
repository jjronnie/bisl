<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;
use Spatie\Permission\Exceptions\UnauthorizedException;
use App\Http\Middleware\EnsurePasswordIsChanged;
use Spatie\Honeypot\ProtectAgainstSpam;
use  App\Http\Middleware\BlockSuspendedUsers;



return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {


        $middleware->appendToGroup('web', [
               ProtectAgainstSpam::class,
            // BlockSuspendedUsers::class,

        ]);





        $middleware->alias([

            'role' => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
            'role_or_permission' => RoleOrPermissionMiddleware::class,
            'pwc' => EnsurePasswordIsChanged::class,

        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (UnauthorizedException $e, $request) {
            return redirect()->back()
                ->with('error', 'You are not authorized to perform this action. Please Contact Admin for authorization.');
        });
    })->create();