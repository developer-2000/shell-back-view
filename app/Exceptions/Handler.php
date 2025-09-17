<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Throwable;

class Handler extends ExceptionHandler
{
    public function register(): void
    {
        $this->renderable(function (Throwable $e, Request $request) {
            if ($e instanceof ValidationException) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $e->errors(), // Возвращаем ошибки валидации
                ], 422);
            }

            if ($request->wantsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $e->getMessage(),
                    'errors' => [],
                ], $this->isHttpException($e) ? $e->getStatusCode() : 500);
            }
        });
    }

    protected function getErrorResponse(string $message = '', array $errors = [], int $code = 400): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'errors' => $errors
        ], $code);
    }

    protected function getStatusCode(Throwable $exception): int
    {
        if ($exception instanceof SymfonyResponse) {
            return $exception->getStatusCode();
        }

        return SymfonyResponse::HTTP_INTERNAL_SERVER_ERROR;
    }
}

