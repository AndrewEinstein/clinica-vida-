<?php

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
        // Behind Render (and most PaaS), TLS is terminated at a proxy/load balancer.
        // Trust forwarded headers so Laravel generates HTTPS URLs and CSRF/session cookies work.
        $middleware->trustProxies(at: '*', headers: Request::HEADER_X_FORWARDED_ALL);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->create();
