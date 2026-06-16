<?php

namespace App\Services\Order;

use App\Exceptions\BaseException;
use App\Http\Requests\Order\InsertOrderRequest;
use App\Http\Requests\Order\InsertPaymentRequest;
use App\Models\Order;
use App\Utils\ResponseUtil;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Throwable;

class InsertPaymentService
{
    /**
     * @throws Throwable
     */
    public function handle(InsertPaymentRequest $request): JsonResponse
    {
        $connection = DB::connection('mysql');
        $connection->beginTransaction();

        try {
            $id_order = $request->validated('id_order');

            $order = Order::query()->findOrFail($id_order);

            $mitrans_auth_token = 'Basic ' . base64_encode( env('MITRANS_SERVER_KEY') . ':' );

            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type'  => 'application/json',
                'Authorization' => $mitrans_auth_token
            ])->withoutVerifying() // DELETE ON PRODUCTION
            ->post('https://app.sandbox.midtrans.com/snap/v1/transactions', [
                'transaction_details' => [
                    'order_id'     => $order->id,
                    'gross_amount' => $order->gross_ammount
                ]
            ]);

            $connection->commit();

            if ($response->failed()) {
                throw new BaseException(
                    message: $response->json()['error_messages'][0] ?? 'Terjadi kesalahan pada server Midtrans.',
                    code: $response->status()
                );
            };

            $data = $response->json();
            return response()->json([
                'message' => 'Berhasil meng-inisiasi payment midtrans',
                'data'=> $data
            ]);

        } catch (Throwable $throwable) {
            $connection->rollBack();
            throw new BaseException(message: $throwable->getMessage(), code: $throwable->getCode());
        }


    }
}
