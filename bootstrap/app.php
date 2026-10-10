
<?php

use App\Exceptions\BusinessException;
use App\Support\ApiErrorResponse;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Illuminate\Validation\ValidationException;

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

        // 1. Return JSON for API requests.
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request, Throwable $e) =>
                $request->is('api/*') || $request->expectsJson()
        );

        // 2. Handle application exceptions.
        $exceptions->render(function (Throwable $e, Request $request) {

            if (! $request->is('api/*')) {
                return null;
            }
// Validation errors: 422
if ($e instanceof ValidationException) {
    return ApiErrorResponse::make(
        code: 'VALIDATION_ERROR',
        message: 'The given data was invalid.',
        status: 422,
        details: $e->errors()
    );
}
            // Business rule violations: 409
            if ($e instanceof BusinessException) {
                return ApiErrorResponse::make(
                    code: $e->errorCode,
                    message: $e->getMessage(),
                    status: $e->status()
                );
            }

            // Authentication errors: 401
            if ($e instanceof AuthenticationException) {
                return ApiErrorResponse::make(
                    code: 'UNAUTHENTICATED',
                    message: 'Authentication is required.',
                    status: 401
                );
            }

            // Missing database resources: 404
            if ($e instanceof ModelNotFoundException) {
                return ApiErrorResponse::make(
                    code: 'RESOURCE_NOT_FOUND',
                    message: 'The requested resource was not found.',
                    status: 404
                );
            }

            // HTTP errors: 404, 403, 405, etc.
            if ($e instanceof HttpExceptionInterface) {
                $status = $e->getStatusCode();

                return ApiErrorResponse::make(
                    code: $status === 404
                        ? 'RESOURCE_NOT_FOUND'
                        : 'HTTP_ERROR',
                    message: $status === 404
                        ? 'The requested resource was not found.'
                        : 'The request could not be completed.',
                    status: $status
                );
            }

            // Unexpected server errors: 500
            return ApiErrorResponse::make(
                code: 'INTERNAL_SERVER_ERROR',
                message: 'An unexpected server error occurred.',
                status: 500
            );
        });

        // 3. Normalize Laravel's default validation JSON response.
        $exceptions->respond(function (Response $response) {

            $request = request();

            if (! $request->is('api/*')) {
                return $response;
            }

            if ($response->getStatusCode() !== 422) {
                return $response;
            }

            $data = json_decode($response->getContent(), true);

            if (
                is_array($data) &&
                isset($data['errors']) &&
                is_array($data['errors'])
            ) {
                return ApiErrorResponse::make(
                    code: 'VALIDATION_ERROR',
                    message: 'The given data was invalid.',
                    status: 422,
                    details: $data['errors']
                );
            }

            return $response;
        });
    })
    ->create();
