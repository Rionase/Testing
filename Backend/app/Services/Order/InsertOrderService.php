<?php

namespace App\Services\Order;

use App\Exceptions\BaseException;
use App\Http\Requests\Order\InsertOrderRequest;
use App\Models\Order;
use App\Utils\ResponseUtil;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Throwable;

class InsertOrderService
{
    /**
     * @throws Throwable
     */
    public function handle(InsertOrderRequest $request): JsonResponse
    {
        $connection = DB::connection('mysql');
        $connection->beginTransaction();

        try {
            $gross_ammount = $request->validated('gross_ammount');
            $keterangan = $request->validated('keterangan');

            $order = Order::query()->create([
                'gross_ammount' => $gross_ammount,
                'keterangan' => $keterangan,
                'midtrans_payment_status' => 'pending'
            ]);

            $connection->commit();

            return ResponseUtil::success(
                data: [ 'order_id' => $order->id ],
                message: 'Berhasil membuat order.'
            );

        } catch (Throwable $throwable) {
            $connection->rollBack();
            throw new BaseException(message: $throwable->getMessage(), code: $throwable->getCode());
        }


    }
}
