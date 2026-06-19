<?php

namespace App\Services\Order;

use App\Exceptions\BaseException;
use App\Exceptions\ValidationException;
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

        $id_order = $request->validated('id_order');
        $order = Order::query()->findOrFail($id_order);

        $mitrans_auth_token = 'Basic ' . base64_encode( env('MIDTRANS_SERVER_KEY') . ':' );
        $midtrans_expiry_duration_minutes = (int) env('MIDTRANS_EXPIRY_DURATION_MINUTES');

        if (!is_null($order->status) && now() > $order->expired_at) {
            throw new ValidationException('Waktu Pembayaran Telah Habis!');

        } else if (is_null($order->status)) {
            // Order sudah dicheckout user, tapi belum initiate snap pembayaran midtrans

            $connection = DB::connection('mysql');
            $connection->beginTransaction();
            try {

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

                if ($response->failed()) {
                    throw new BaseException(
                        message: $response->json()['error_messages'][0] ?? 'Terjadi kesalahan pada server Midtrans.',
                        code: $response->status()
                    );
                };

                $token = $response->json()['token'];
                $redirect_url = $response->json()['redirect_url'];

                $order->update([
                    'status' => 'pending',
                    'snap_token' => $token,
                    'snap_redirect_url' => $redirect_url,
                    'expired_at' => now()->addMinutes($midtrans_expiry_duration_minutes)
                ]);

                $connection->commit();
            } catch (Throwable $throwable) {
                $connection->rollBack();
                throw new BaseException(message: $throwable->getMessage(), code: $throwable->getCode());
            }

        } else if ($order->status == 'pending' && now() <= $order->expired_at) {
            $token = $order->snap_token;
            $redirect_url = $order->snap_redirect_url;

        } else {
            throw new BaseException('Terjadi kesalahan pada server API.', 500);

        }

        return response()->json([
            'message' => 'Berhasil mendapatkan data pembayaran midtrans.',
            'data'=> [
                'token' => $token,
                'redirect_url' => $redirect_url,
            ]
        ]);
    }
}
