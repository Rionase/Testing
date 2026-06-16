<?php

namespace App\Http\Controllers;

use App\Utils\ResponseUtil;
use Illuminate\Http\JsonResponse;

class Controller
{
    public function getLanding(): JsonResponse
    {
        return ResponseUtil::success(message: 'Berhasil landing.');
    }
}
