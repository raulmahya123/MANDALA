<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// (opsional) kalau mau pakai class name pendek, bisa tambahkan:
// use App\Http\Middleware\EnsureRole;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        // kalau kamu punya api routes, isi juga param api:
        // api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // ✅ Daftarkan alias middleware 'role'
        $middleware->alias([
            // pakai FQCN biar jelas:
            'role' => \App\Http\Middleware\EnsureRole::class,
        ]);

        // (opsional) kalau ingin menambahkan middleware ke group 'web' atau 'api'
        // $middleware->web(append: [
        //     \Illuminate\Session\Middleware\AuthenticateSession::class,
        // ]);
        // $middleware->api(prepend: [
        //     \Illuminate\Routing\Middleware\SubstituteBindings::class,
        // ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // (opsional) custom error handling di sini
        // $exceptions->render(function (\Throwable $e, $request) {
        //     return response()->view('errors.custom', [], 500);
        // });
    })
    ->create();
