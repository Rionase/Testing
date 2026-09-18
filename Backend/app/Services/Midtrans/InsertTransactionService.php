<?php

namespace App\Services\Midtrans;

use App\Exceptions\BaseException;
use App\Http\Requests\Midtrans\InsertTransactionRequest;
use App\Utils\MidtransUtil;
use Exception;
use Illuminate\Http\JsonResponse;
use Throwable;

class InsertTransactionService
{
    /**
     * @throws Exception
     */
    public function handle(InsertTransactionRequest $request): JsonResponse
    {
        try {
            $params = $request->validated('params');

            $result = MidtransUtil::insertMidtransTransaction($params);

            return response()->json([
                'message' => 'success',
                'data'=> $result
            ]);

        } catch (Throwable $throwable) {
            throw new BaseException(message: $throwable->getMessage(), code: $throwable->getCode());
        }
    }
}
