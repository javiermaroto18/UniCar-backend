<?php

use App\Exceptions\ApiException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Auth\AuthenticationException;
use Spatie\Permission\Exceptions\UnauthorizedException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )

    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);
    })

    ->withExceptions(function (Exceptions $exceptions): void {           

        // Excepciones personalizadas para la API
        $exceptions->render(function (ApiException $e, Request $request) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => $e->getErrorCode(),
                    'message' => $e->getMessage(),
                    'data' => $e->getData(),
                ],
            ], $e->getStatusCode());
        });

        // Error de no autenticado (401 - Sanctum)
        $exceptions->render(function (AuthenticationException $e, Request $request) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'UNAUTHORIZED',
                    'message' => 'No tienes permisos. Por favor, inicia sesión.',
                    'data' => [],
                ],
            ], 401);
        });

        // Errores de validación (422)
        $exceptions->render(function (ValidationException $e, Request $request) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'VALIDATION_ERROR',
                    'message' => 'Datos no válidos',
                    'data' => $e->errors(),
                ],
            ], 422);
        });

        // Eror de modelo no encontrado con su nombre (404)
        $exceptions->render(function (ModelNotFoundException $e, Request $request) {
            $model = class_basename($e->getModel());

            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'MODEL_NOT_FOUND',
                    'message' => "{$model} no encontrado",
                    'data' => [],
                ],
            ], 404);
        });

        // Error de ruta no encontrada (404)
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'ROUTE_NOT_FOUND',
                    'message' => 'Ruta no encontrada',
                    'data' => [],
                ],
            ], 404);
        });

        // Error de permisos no autorizados (403)
        $exceptions->render(function (UnauthorizedException $e, Request $request) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'UNAUTHORIZED',
                    'message' => 'No tienes permisos para realizar esta acción',
                    'data' => [],
                ],
            ], 403);
        });
    })
    ->create();