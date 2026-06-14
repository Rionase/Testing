<?php

namespace App\Utils;

use Illuminate\Http\JsonResponse;

class ResponseUtil
{
    public static function error(string $message, mixed $code = 500): JsonResponse
    {
        $code = (is_int($code) && $code >= 100 && $code < 600) ? $code : 500;
        return response()->json([
            'message' => $message
        ], $code);
    }

    public static function success($data = null, string $message = null, int $code = 200): JsonResponse
    {
        return response()->json([
            'message' => $message,
            'data' => $data
        ], $code);
    }
}
