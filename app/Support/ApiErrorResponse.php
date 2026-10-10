<?php

namespace App\Support;

use Illuminate\Http\JsonResponse;

final class ApiErrorResponse
{
    public static function make(
        string $code,
        string $message,
        int $status,
        array $details = []
    ): JsonResponse {
        return response()->json([
            'success' => false,
            'error' => [
                'code' => $code,
                'message' => $message,
                'details' => $details,
            ],
        ], $status);
    }
}
