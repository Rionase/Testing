<?php

namespace App\Utils;

use App\Exceptions\BaseException;
use Exception;
use Illuminate\Support\Facades\Http;
use InvalidArgumentException;

class MidtransUtil
{
    /**
     * @return string
     */
    static public function getMidtransAuthToken(): string
    {
        return 'Basic ' . base64_encode( env('MIDTRANS_SERVER_KEY') . ':' );
    }

    /**
     * @param array $payload
     * @return array
     * @throws Exception
     */
    static public function insertMidtransTransaction(array $payload): array
    {
        self::sanitizeInsertMidtransTransactionPayload($payload);
        self::validateInsertMidtransTransactionPayload($payload);

        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type'  => 'application/json',
            'Authorization' => self::getMidtransAuthToken()
        ])->withoutVerifying() // DELETE ON PRODUCTION
        ->post('https://app.sandbox.midtrans.com/snap/v1/transactions', $payload);

        if ($response->failed()) {
            throw new BaseException(
                message: $response->json()['error_messages'][0] ?? 'Error on Midtrans server.',
                code: $response->status()
            );
        };

        return $response->json();
    }

    public const INSERT_TRANSACTION_FIELD_LIMIT = [
        'item_details.name'            => 50,
        'customer_details.first_name'  => 255,
        'customer_details.last_name'   => 255,
        'customer_details.email'       => 255,
        'customer_details.phone'       => 255,
    ];

    /**
     * Trim string field that exceed limit for InsertMidtransTransaction
     *
     * @param array $payload
     * @return void
     */
    static public function sanitizeInsertMidtransTransactionPayload(array &$payload): void
    {
        // 1. Sanitize customer_details
        if (isset($payload['customer_details']) && is_array($payload['customer_details'])) {
            $first_name = $payload['customer_details']['first_name'] ?? null;
            if (is_string($first_name)) {
                $payload['customer_details']['first_name'] = mb_substr($first_name, 0, self::INSERT_TRANSACTION_FIELD_LIMIT['customer_details.first_name']);
            }

            $last_name = $payload['customer_details']['last_name'] ?? null;
            if (is_string($last_name)) {
                $payload['customer_details']['last_name'] = mb_substr($last_name, 0, self::INSERT_TRANSACTION_FIELD_LIMIT['customer_details.last_name']);
            }

            $email = $payload['customer_details']['email'] ?? null;
            if (is_string($email)) {
                $payload['customer_details']['email'] = mb_substr($email, 0, self::INSERT_TRANSACTION_FIELD_LIMIT['customer_details.email']);
            }

            $phone = $payload['customer_details']['phone'] ?? null;
            if (is_string($phone)) {
                $payload['customer_details']['phone'] = mb_substr($phone, 0, self::INSERT_TRANSACTION_FIELD_LIMIT['customer_details.phone']);
            }
        }

        // 2. Sanitize item_details.name
        if (isset($payload['item_details']) && is_array($payload['item_details'])) {
            $name_limit = self::INSERT_TRANSACTION_FIELD_LIMIT['item_details.name'];

            foreach ($payload['item_details'] as $index => $item) {
                $item_name = $item['name'] ?? null;
                if (is_string($item_name)) {
                    $payload['item_details'][$index]['name'] = mb_substr($item_name, 0, $name_limit);
                }
            }
        }
    }

    /**
     * Validate insertMidtransTransaction payload before HIT Midtrans API.
     * Midtrans Documentation: https://docs.midtrans.com/reference/request-body-json-parameter
     *
     * Validated Payload Structure: ```
     * {
     *  "transaction_details": {
     *      "order_id": string,
     *      "gross_amount": int|float
     *  },
     *  "item_details": [
     *      {
     *          "id": string|null,
     *          "name": string,
     *          "price": int|float,
     *          "quantity": int
     *      }
     *  ],
     *  "customer_details": {
     *      "first_name": string|null,
     *      "last_name": string|null,
     *      "email": string|null,
     *      "phone": string|null
     *  },
     * // Expiration for End Payment Page after Choosing payment method
     *  "expiry": {
     *      "start_time": string|null,
     *      "unit": 'day' | 'days' | 'hour' | 'hours' | 'minute' | 'minutes',
     *      "duration": int
     *  },
     *  // Expiration for Choose payment method
     *  "page_expiry": {
     *      "unit": 'day' | 'days' | 'hour' | 'hours' | 'minute' | 'minutes',
     *      "duration": int
     *  }
     * } ```
     *
     * @param array $payload
     * @return void
     */
    static public function validateInsertMidtransTransactionPayload(array $payload): void
    {
        // 1. Validate transaction_details (Required)
        $transaction_details = $payload['transaction_details'] ?? null;
        if (empty($transaction_details) || !is_array($transaction_details)) {
            throw new InvalidArgumentException("Field 'transaction_details' is required and must be an array.");
        }

        // transaction_details.order_id doesn't need validation

        $gross_amount = $transaction_details['gross_amount'] ?? null;
        if (!is_numeric($gross_amount) || $gross_amount <= 0) {
            throw new InvalidArgumentException("Field 'transaction_details.gross_amount' must be a positive number.");
        }

        // 2. Validate item_details (Optional, including subtotal check)
        if (isset($payload['item_details'])) {
            if (!is_array($payload['item_details'])) {
                throw new InvalidArgumentException("Field 'item_details' must be an array.");
            }

            $calculated_subtotal = 0;
            $item_name_limit = self::INSERT_TRANSACTION_FIELD_LIMIT['item_details.name'];

            foreach ($payload['item_details'] as $index => $item) {
                // item_details.id doesn't need validation

                if (!isset($item['price'], $item['quantity'], $item['name'])) {
                    throw new InvalidArgumentException("Each item in 'item_details' (index {$index}) must contain 'price', 'quantity', and 'name'.");
                }

                if (!is_numeric($item['price'])) {
                    throw new InvalidArgumentException("Item price at index {$index} must be a number.");
                }

                if (!is_int($item['quantity']) || $item['quantity'] < 1) {
                    throw new InvalidArgumentException("Item quantity at index {$index} must be an integer of at least 1.");
                }

                if (mb_strlen((string) $item['name']) > $item_name_limit) {
                    throw new InvalidArgumentException("Item name at index {$index} must not exceed {$item_name_limit} characters.");
                }

                $calculated_subtotal += ((int) $item['price'] * (int) $item['quantity']);
            }

            if ((int) $calculated_subtotal !== (int) $gross_amount) {
                throw new InvalidArgumentException("Total calculated subtotal in item_details ({$calculated_subtotal}) does not match transaction_details.gross_amount ({$gross_amount}).");
            }
        }

        // 3. Validate customer_details (Optional)
        if (isset($payload['customer_details'])) {
            if (!is_array($payload['customer_details'])) {
                throw new InvalidArgumentException("Field 'customer_details' must be an array.");
            }

            $customer = $payload['customer_details'];

            if (isset($customer['first_name'])) {
                $limit = self::INSERT_TRANSACTION_FIELD_LIMIT['customer_details.first_name'];
                if (mb_strlen((string) $customer['first_name']) > $limit) {
                    throw new InvalidArgumentException("Field 'customer_details.first_name' exceeds maximum length of {$limit} characters.");
                }
            }

            if (isset($customer['last_name'])) {
                $limit = self::INSERT_TRANSACTION_FIELD_LIMIT['customer_details.last_name'];
                if (mb_strlen((string) $customer['last_name']) > $limit) {
                    throw new InvalidArgumentException("Field 'customer_details.last_name' exceeds maximum length of {$limit} characters.");
                }
            }

            if (isset($customer['email'])) {
                $limit = self::INSERT_TRANSACTION_FIELD_LIMIT['customer_details.email'];
                if (mb_strlen((string) $customer['email']) > $limit) {
                    throw new InvalidArgumentException("Field 'customer_details.email' exceeds maximum length of {$limit} characters.");
                }
            }

            if (isset($customer['phone'])) {
                $limit = self::INSERT_TRANSACTION_FIELD_LIMIT['customer_details.phone'];
                if (mb_strlen((string) $customer['phone']) > $limit) {
                    throw new InvalidArgumentException("Field 'customer_details.phone' exceeds maximum length of {$limit} characters.");
                }
            }
        }

        // 4. Validate expiry (Optional)
        if (isset($payload['expiry'])) {
            if (!is_array($payload['expiry'])) {
                throw new InvalidArgumentException("Field 'expiry' must be an array.");
            }

            $expiry = $payload['expiry'];
            $valid_units = ['day', 'days', 'hour', 'hours', 'minute', 'minutes'];

            if (empty($expiry['duration']) || !is_int($expiry['duration']) || $expiry['duration'] < 1) {
                throw new InvalidArgumentException("Field 'expiry.duration' must be a positive integer.");
            }

            if (empty($expiry['unit']) || !in_array($expiry['unit'], $valid_units, true)) {
                throw new InvalidArgumentException("Field 'expiry.unit' must be one of the following: " . implode(', ', $valid_units));
            }

            if (isset($expiry['start_time']) && !preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2} \+\d{4}$/', $expiry['start_time'])) {
                throw new InvalidArgumentException("Format for 'expiry.start_time' is invalid. Expected format: 'yyyy-MM-dd HH:mm:ss Z'.");
            }
        }

        // 5. Validate page_expiry (Optional)
        if (isset($payload['page_expiry'])) {
            if (!is_array($payload['page_expiry'])) {
                throw new InvalidArgumentException("Field 'page_expiry' must be an array.");
            }

            $page_expiry = $payload['page_expiry'];
            $valid_units = ['day', 'days', 'hour', 'hours', 'minute', 'minutes'];

            if (empty($page_expiry['duration']) || !is_int($page_expiry['duration']) || $page_expiry['duration'] < 1) {
                throw new InvalidArgumentException("Field 'page_expiry.duration' must be a positive integer.");
            }

            if (empty($page_expiry['unit']) || !in_array($page_expiry['unit'], $valid_units, true)) {
                throw new InvalidArgumentException("Field 'page_expiry.unit' must be one of the following: " . implode(', ', $valid_units));
            }
        }
    }
}
