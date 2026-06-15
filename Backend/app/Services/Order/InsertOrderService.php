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
    public function handle(InsertOrderRequest $request): JsonResponse
    {
        $connection = DB::connection('mysql');
        $connection->beginTransaction();

        try {
            $order_id = $request->validated('order_id');
            $gross_ammount = $request->validated('gross_ammount');
            $keterangan = $request->validated('keterangan');

            Order::query()->create([
                'order_id' => $order_id,
                'gross_ammount' => $gross_ammount,
                'keterangan' => $keterangan
            ]);

            $mitrans_auth_token = 'Basic ' . base64_encode( env('MITRANS_SERVER_KEY') . ':' );

            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type'  => 'application/json',
                'Authorization' => $mitrans_auth_token
            ])->withoutVerifying() // DELETE ON PRODUCTION
            ->post('https://app.sandbox.midtrans.com/snap/v1/transactions', [
                'transaction_details' => [
                    'order_id'     => $order_id,
                    'gross_amount' => $gross_ammount
                ]
            ]);

            if ($response->failed()) {
                throw new BaseException(
                    message: $response->json() ?? 'Terjadi kesalahan pada server Midtrans.',
                    code: $response->status()
                );
            };

            $data = $response->json();

            $connection->commit();

            return ResponseUtil::success(
                data: $data,
                message: 'Berhasil memmbuat order.'
            );


        } catch (Throwable $throwable) {
            $connection->rollBack();
            throw new BaseException(message: $throwable->getMessage(), code: $throwable->getCode());
        }


    }
}
