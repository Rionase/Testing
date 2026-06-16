<?php

namespace App\Services\Mitrans;

use App\Exceptions\BaseException;
use App\Http\Requests\Mitrans\InsertTransactionRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Throwable;

class InsertTransactionService
{
    public function handle(InsertTransactionRequest $request): JsonResponse
    {
        try {
            $order_id = $request->validated('order_id');
            $gross_ammount = $request->validated('gross_ammount');

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
                    message: $response->json()['error_messages'][0] ?? 'Terjadi kesalahan pada server Midtrans.',
                    code: $response->status()
                );
            };

            $data = $response->json();
            return response()->json([
                'message' => 'success',
                'data'=> $data
            ]);

        } catch (Throwable $throwable) {
            throw new BaseException(message: $throwable->getMessage(), code: $throwable->getCode());
        }
    }
}
