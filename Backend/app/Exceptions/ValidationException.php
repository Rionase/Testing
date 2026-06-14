<?php

namespace App\Exceptions;

use App\Utils\ResponseUtil;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ValidationException extends Exception
{
    protected $code = 422;

    public function __construct(string $message)
    {
        parent::__construct($message);
    }

    public function render(Request $request): JsonResponse
    {
        return ResponseUtil::error(
            message: $this->message,
            code: $this->code
        );
    }
}
