<?php

use App\Exceptions\DataMasihDipakai;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->redirectGuestsTo(fn () => route('admin.masuk'));
        $middleware->redirectUsersTo(fn () => route('admin.dasbor'));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        // Penolakan hapus karena data masih dirujuk adalah kondisi wajar, bukan galat sistem.
        $exceptions->dontReport(DataMasihDipakai::class);
        $exceptions->render(fn (DataMasihDipakai $e, Request $request) => $request->expectsJson()
            ? null
            : back()->with('galat', $e->getMessage()));
    })->create();
