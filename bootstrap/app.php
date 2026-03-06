<?php
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
        ]);
        $middleware->trustProxies(at: '*');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

// Forcer le storage vers /tmp sur Vercel
if (isset($_ENV['VERCEL'])) {
    @mkdir('/tmp/storage/logs', 0777, true);
    @mkdir('/tmp/storage/framework/views', 0777, true);
    @mkdir('/tmp/storage/framework/cache/data', 0777, true);
    @mkdir('/tmp/storage/framework/sessions', 0777, true);
    $app->useStoragePath('/tmp/storage');
}

return $app;
