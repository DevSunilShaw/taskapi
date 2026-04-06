<?php
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;


return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {

        
      
        // Authentication Exception (401)
        
        
        $exceptions->render(function (AuthenticationException $e, $request) {
            if ($request->expectsJson()) {

                Log::warning('Unauthenticated access attempt', [
                    'url' => $request->fullUrl(),
                    'ip'  => $request->ip(),
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated.',
                ], 401);
            }
        });

     
        // Validation Exception (422)
        
        $exceptions->render(function (ValidationException $e, $request) {
            if ($request->expectsJson()) {

                Log::info('Validation failed', [
                    'errors' => $e->errors(),
                    'input'  => $request->all(),
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors'  => $e->errors(),
                ], 422);
            }
        });

        // HTTP Exceptions (404, 403 etc.)
        
        $exceptions->render(function (HttpExceptionInterface $e, $request) {
            if ($request->expectsJson()) {

                Log::error('HTTP Exception', [
                    'status'  => $e->getStatusCode(),
                    'message' => $e->getMessage(),
                ]);

                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage() ?: 'HTTP Error',
                ], $e->getStatusCode());
            }
        });

       // Global Exception (500)
       
        $exceptions->render(function (\Throwable $e, $request) {
            if ($request->expectsJson()) {

                Log::critical('Server Error', [
                    'message' => $e->getMessage(),
                    'file'    => $e->getFile(),
                    'line'    => $e->getLine(),
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Server Error',
                ], 500);
            }
        });

    
    })->create();
