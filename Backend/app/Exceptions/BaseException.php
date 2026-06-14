<?php

namespace App\Exceptions;

use App\Utils\ResponseUtil;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BaseException extends Exception
{
    public function __construct(string $message, mixed $code = 500)
    {
        parent::__construct($message);
        $this->code = $code;
    }

    public function render(Request $request): JsonResponse
    {
        return ResponseUtil::error(
            message: $this->message,
            code: $this->code
        );
    }
}
